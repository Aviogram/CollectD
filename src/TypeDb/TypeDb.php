<?php
namespace Aviogram\CollectD\TypeDb;

use Aviogram\CollectD\Exception\TypeDbNotFound;

class TypeDb
{
    /**
     * @var string
     */
    protected $location;

    /**
     * @var Collection\TypeDb
     */
    protected $types;

    /**
     * @var array
     */
    private $exceptions = array(
        1 => 'File %s does not exists.',
    );

    /**
     * @param string $location The location of the types.db file (/usr/share/collectd/types.db)
     */
    public function __construct($location = '/usr/share/collectd/types.db')
    {
        $this->location = $location;

        if (is_file($this->location) === false) {
            throw new TypeDbNotFound(sprintf($this->exceptions[1], $this->location), 1);
        }
    }

    /**
     * @return Collection\TypeDb
     */
    public function getTypeDb()
    {
        if ($this->types === null) {
            $this->types = $this->parseTypeDbFile();
        }

        return $this->types;
    }

    protected function parseTypeDbFile()
    {
        $return     = new Collection\TypeDb();
        $lineRegex  = '/^(?!#)(?P<plugin>[^\s]+)\s+(?P<settings>.*)$/';
        $valueRegex = '/(?P<name>[^:]+):(?P<type>[^:]+):(?P<min>[^:]+):(?P<max>[^\s,]+)(?:, )?/';

        $handler = fopen($this->location, 'r');

        while (($line = fgets($handler))) {
            // Skip comment lines
            if (((boolean) preg_match($lineRegex, $line, $matches)) === false) {
                continue;
            }

            $pluginName = $matches['plugin'];
            $settings   = $matches['settings'];

            // Grep the values
            if (((boolean) preg_match_all($valueRegex, $settings, $matches, PREG_SET_ORDER)) === false) {
                continue;
            }

            $plugin = new Entity\Plugin($pluginName);
            $return->offsetSet($pluginName, $plugin);

            foreach ($matches as $match) {
                $pluginValue = new Entity\PluginValue(
                    $match['name'],
                    $match['type'],
                    (float) $match['min'],
                    ($match['max'] === 'U') ? null : (float) $match['max']
                );

                $plugin->getValues()->append($pluginValue);
            }
        }

        fclose($handler);

        return $return;
    }
}
