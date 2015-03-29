<?php
namespace Aviogram\CollectD\Parser;

use Aviogram\CollectD\Parser\Collection\Packet;

class ServerParser
{
    const HEADER_LENGTH = 4;

    const TYPE_HOST                     = 0,    // String
          TYPE_TIME                     = 1,    // Long
          TYPE_PLUGIN                   = 2,    // String
          TYPE_PLUGIN_INSTANCE          = 3,    // String
          TYPE_TYPE                     = 4,    // String
          TYPE_TYPE_INSTANCE            = 5,    // String
          TYPE_VALUES                   = 6,    // Values Parts
          TYPE_INTERVAL                 = 7,    // Long
          TYPE_TIME_HIGH_RESOLUTION     = 8,    // Long Long
          TYPE_INTERVAL_HIGH_RESOLUTION = 9,    // Long Long
          TYPE_MESSAGE                  = 256,  // String
          TYPE_SEVERITY                 = 257,  // Long
          TYPE_SIGNATURE                = 512,  // Reserved
          TYPE_ENCRYPTION               = 528;  // Reserved

    const VALUE_TYPE_COUNTER  = 0,  // Long Long (unsigned, Big Endian)
          VALUE_TYPE_GAUGE    = 1,  // Long      (unsigned, Little Endian)
          VALUE_TYPE_DERIVE   = 2,  // Long Long (signed, Big Endian)
          VALUE_TYPE_ABSOLUTE = 3;  // Long Long (unsigned, Big Endian)

    private $exceptions = array(
        1 => 'signature packet',
        2 => 'encrypted packet',
    );


    /**
     * Parse server data
     *
     * @param string $data  Binary data
     *
     * @return Packet
     * @throws Exception\UnSupported
     */
    public function parse($data)
    {
        $return     = new Packet();
        $part       = new Entity\Packet();
        $byteStream = new ByteStream($data);

        while ($byteStream->length() > 0) {
            $type         = $byteStream->short();
            $partLength   = $byteStream->short();
            $length       = $partLength - static::HEADER_LENGTH;

            switch ($type) {
                case static::TYPE_HOST:
                    $host = $byteStream->string($length);
                    $part->setHost($host);
                    break;
                case static::TYPE_TIME:
                    $time = $byteStream->longLong();

                    $dateTime = new \DateTime();
                    $dateTime->setTimestamp($time);

                    $part->getTime()->setNormal($dateTime);
                    break;
                case static::TYPE_PLUGIN:
                    $plugin = $byteStream->string($length);
                    $part->getPlugin()->setName($plugin);
                    break;
                case static::TYPE_PLUGIN_INSTANCE:
                    $pluginInstance = $byteStream->string($length);
                    $part->getPlugin()->setInstanceName($pluginInstance);
                    break;
                case static::TYPE_TYPE:
                    $type = $byteStream->string($length);
                    $part->getType()->setName($type);
                    break;
                case static::TYPE_TYPE_INSTANCE:
                    $typeInstance = $byteStream->string($length);
                    $part->getType()->setInstanceName($typeInstance);
                    break;
                case static::TYPE_VALUES:
                    $amountValues = $byteStream->short();
                    $types        = array();

                    // Reset all the values of the part section
                    $part->resetValues();

                    for ($i = 0; $i < $amountValues; $i++) {
                        $types[$i] = $byteStream->byte();
                    }

                    for ($i = 0; $i < $amountValues; $i++) {
                        switch ($types[$i]) {
                            case static::VALUE_TYPE_COUNTER:
                                $value = $byteStream->longLong();
                                break;
                            case static::VALUE_TYPE_GAUGE:
                                $value = $byteStream->double(8);
                                break;
                            case static::VALUE_TYPE_DERIVE:
                                $value = $byteStream->longLong(false);
                                break;
                            case static::VALUE_TYPE_ABSOLUTE:
                                $value = $byteStream->longLong();
                                break;
                            default:
                                throw new Exception\UnSupported('Unknown value type', 4);
                        }

                        $part->addValue(new Entity\Value($types[$i], $value));
                    }

                    $oldPart  = $part;
                    $part     = clone $oldPart;
                    $return->append($oldPart);

                    break;
                case static::TYPE_INTERVAL:
                    $interval = $byteStream->longLong();

                    $part->getInterval()->setNormal($interval);
                    break;
                case static::TYPE_TIME_HIGH_RESOLUTION:
                    $timeHS = $byteStream->longLong();

                    $dateTime = new \DateTime();
                    $dateTime->setTimestamp($timeHS / 1073741824);

                    $part->getTime()->setHighResolution($dateTime);
                    break;
                case static::TYPE_INTERVAL_HIGH_RESOLUTION:
                    $intervalHS = $byteStream->longLong();
                    $part->getInterval()->setHighResolution($intervalHS / 1073741824);
                    break;
                case static::TYPE_MESSAGE:
                    $message = $byteStream->string($length);
                    $part->setMessage($message);
                    break;
                case static::TYPE_SEVERITY:
                    $severity = $byteStream->longLong();
                    $part->setSeverity($severity);
                    break;
                case static::TYPE_SIGNATURE:
                    throw new Exception\UnSupported('Signature packet', 1);
                case static::TYPE_ENCRYPTION:
                    throw new Exception\UnSupported('Encrypted packet', 2);
                default:
                    throw new Exception\UnSupported('Unknown packet', 3);
            }
        }

        // When no parts has been set yet we will set it
        if ($part !== null && $return->count() === 0) {
            $return->append($part);
        }

        return $return;
    }
}
