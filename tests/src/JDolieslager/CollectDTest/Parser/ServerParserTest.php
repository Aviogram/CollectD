<?php
namespace Aviogram\CollectDTest\Parser;

use Aviogram\CollectD\Parser\ServerParser;

class ServerParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServerParser
     */
    protected $parser;

    /**
     * Setup the test case
     */
    protected function setUp()
    {
        $this->parser = new ServerParser();
    }

    public function testParseVersion5()
    {
        $hex = '0000000f6c6170746f702e6c616e00' .                   // Hostname laptop.lan
               '0008000c14508fbe7382517e' .                         // time: hi res
               '0009000c0000000280000000' .                         // interval: hi res
               '0101000c0000000051423EF9' .         	   			// Severity
               '0002000b6d656d6f727900' .                           // plugin memory
               '0005000a776972656400' .                             // type instance: wired
               '0006000f000101000000000043cf41' .                   // value
               '0008000c14508fbe7382949a' .                         // time: hi res
               '0002000e696e7465726661636500' .                     // plugin: interface
               '000300086c6f3000' .                                 // lo0
               '0004000e69665f6f637465747300' .                     // type: if_octects
               '0005000500' .                                       // Type instance: null
               '0006001800020202000000000088078b000000000088078c' . // 2 values
               '0008000c14508fbe7384406c' .                         // time: hi res
               '0004000f69665f7061636b65747300' .                   // plugin: ifpackets
               '000600180002020300000000000000000000000000000000';  // 2 values

        $binaryData = hex2bin($hex);

        $result = $this->parser->parse($binaryData);

        $this->assertInstanceOf('Aviogram\CollectD\Parser\Collection\Packet', $result);
        $this->assertCount(3, $result);

        foreach ($result as $part) {
            $this->assertInstanceOf('Aviogram\CollectD\Parser\Entity\Packet', $part);
        }

        // Check first part
        $this->assertEquals('laptop.lan', $result[0]->getHost());
        $this->assertEquals('memory', $result[0]->getPlugin()->getName());
        $this->assertEquals(null, $result[0]->getPlugin()->getInstanceName());
        $this->assertEquals(null, $result[0]->getType()->getName());
        $this->assertEquals('wired', $result[0]->getType()->getInstanceName());
        $this->assertEquals(null, $result[0]->getTime()->getNormal());
        $this->assertEquals(null, $result[0]->getTime()->getNormal());

        $this->assertInstanceOf('DateTime', $result[0]->getTime()->getHighResolution());
        $this->assertEquals('2013-03-14 21:19:53.000000', $result[0]->getTime()->getHighResolution()->format('Y-m-d H:i:s.u'));

        $this->assertEquals(null, $result[0]->getInterval()->getNormal());
        $this->assertEquals(10, $result[0]->getInterval()->getHighResolution());
        $this->assertEquals(1363295993, $result[0]->getSeverity());

        // Check the value[0] values part
        $this->assertCount(1, $result[0]->getValues());

        foreach ($result[0]->getValues() as $value) {
            $this->assertInstanceOf('Aviogram\CollectD\Parser\Entity\Value', $value);
        }

        $this->assertEquals(true, $result[0]->getValues()[0]->isGauge());
        $this->assertEquals(false, $result[0]->getValues()[0]->isAbsolute());
        $this->assertEquals(false, $result[0]->getValues()[0]->isCounter());
        $this->assertEquals(false, $result[0]->getValues()[0]->isDerive());
        $this->assertEquals(1048969216, $result[0]->getValues()[0]->getValue());


        // Check second part
        $this->assertEquals('laptop.lan', $result[1]->getHost());
        $this->assertEquals('interface', $result[1]->getPlugin()->getName());
        $this->assertEquals('lo0', $result[1]->getPlugin()->getInstanceName());
        $this->assertEquals('if_octets', $result[1]->getType()->getName());
        $this->assertEquals(null, $result[1]->getType()->getInstanceName());
        $this->assertEquals(null, $result[1]->getTime()->getNormal());
        $this->assertEquals(null, $result[1]->getTime()->getNormal());

        $this->assertInstanceOf('DateTime', $result[1]->getTime()->getHighResolution());
        $this->assertEquals('2013-03-14 21:19:53.000000', $result[1]->getTime()->getHighResolution()->format('Y-m-d H:i:s.u'));

        $this->assertEquals(null, $result[1]->getInterval()->getNormal());
        $this->assertEquals(10, $result[1]->getInterval()->getHighResolution());
        $this->assertEquals(1363295993, $result[1]->getSeverity());

        // Check the value[0] values part
        $this->assertCount(2, $result[1]->getValues());

        foreach ($result[1]->getValues() as $value) {
            $this->assertInstanceOf('Aviogram\CollectD\Parser\Entity\Value', $value);
        }

        $this->assertEquals(false, $result[1]->getValues()[0]->isGauge());
        $this->assertEquals(false, $result[1]->getValues()[0]->isAbsolute());
        $this->assertEquals(false, $result[1]->getValues()[0]->isCounter());
        $this->assertEquals(true, $result[1]->getValues()[0]->isDerive());
        $this->assertEquals(8914827, $result[1]->getValues()[0]->getValue());

        $this->assertEquals(false, $result[1]->getValues()[1]->isGauge());
        $this->assertEquals(false, $result[1]->getValues()[1]->isAbsolute());
        $this->assertEquals(false, $result[1]->getValues()[1]->isCounter());
        $this->assertEquals(true, $result[1]->getValues()[1]->isDerive());
        $this->assertEquals(8914828, $result[1]->getValues()[1]->getValue());

        // Check third part
        $this->assertEquals('laptop.lan', $result[2]->getHost());
        $this->assertEquals('interface', $result[2]->getPlugin()->getName());
        $this->assertEquals('lo0', $result[2]->getPlugin()->getInstanceName());
        $this->assertEquals('if_packets', $result[2]->getType()->getName());
        $this->assertEquals(null, $result[2]->getType()->getInstanceName());
        $this->assertEquals(null, $result[2]->getTime()->getNormal());
        $this->assertEquals(null, $result[2]->getTime()->getNormal());

        $this->assertInstanceOf('DateTime', $result[2]->getTime()->getHighResolution());
        $this->assertEquals('2013-03-14 21:19:53.000000', $result[2]->getTime()->getHighResolution()->format('Y-m-d H:i:s.u'));

        $this->assertEquals(null, $result[2]->getInterval()->getNormal());
        $this->assertEquals(10, $result[2]->getInterval()->getHighResolution());
        $this->assertEquals(1363295993, $result[2]->getSeverity());

        // Check the value[0] values part
        $this->assertCount(2, $result[2]->getValues());

        foreach ($result[2]->getValues() as $value) {
            $this->assertInstanceOf('Aviogram\CollectD\Parser\Entity\Value', $value);
        }

        $this->assertEquals(false, $result[2]->getValues()[0]->isGauge());
        $this->assertEquals(false, $result[2]->getValues()[0]->isAbsolute());
        $this->assertEquals(false, $result[2]->getValues()[0]->isCounter());
        $this->assertEquals(true, $result[2]->getValues()[0]->isDerive());
        $this->assertEquals(0, $result[2]->getValues()[0]->getValue());

        $this->assertEquals(false, $result[2]->getValues()[1]->isGauge());
        $this->assertEquals(true, $result[2]->getValues()[1]->isAbsolute());
        $this->assertEquals(false, $result[2]->getValues()[1]->isCounter());
        $this->assertEquals(false, $result[2]->getValues()[1]->isDerive());
        $this->assertEquals(0, $result[2]->getValues()[1]->getValue());
    }

    public function testVersion4()
    {
        $hex = '0000000f6c6170746f702e6c616e00' .		   		 // hostname: 'laptop.lan'
            '0001000c0000000051423EF9' .         	   			 // time . low res
            '0007000c000000000000000A' .         	   			 // interval
            '0002000b6d656d6f727900' .            	   			 // plugin: memory
            '0005000a776972656400' .               	   			 // type instance: wired
            '0006000f000101000000000043cf41' .		   		     // value
            '0001000c0000000051423EFA' .         	   			 // time . low res
            '0002000e696e7465726661636500' .   		   		     // plugin: interface
            '000300086c6f3000' .                        		 // instance: lo0
            '0004000e69665f6f637465747300' .   					 // type: if_octets
            '0005000500' .                              		 // type instance: nil
            '0006001800020202000000000088078b000000000088078c' . // 2 more values . note: the second one was manipulated to check order
            '0004000f69665f7061636b65747300' .                   // plugin: ifpackets
            '000600180002020200000000000000000000000000000000';  // 2 more values

        $result = $this->parser->parse(hex2bin($hex));

        $this->assertInstanceOf('Aviogram\CollectD\Parser\Collection\Packet', $result);
        $this->assertCount(3, $result);

        foreach ($result as $part) {
            $this->assertInstanceOf('Aviogram\CollectD\Parser\Entity\Packet', $part);
        }

        // Check first part
        $this->assertEquals('laptop.lan', $result[0]->getHost());
        $this->assertEquals('memory', $result[0]->getPlugin()->getName());
        $this->assertEquals(null, $result[0]->getPlugin()->getInstanceName());
        $this->assertEquals(null, $result[0]->getType()->getName());
        $this->assertEquals('wired', $result[0]->getType()->getInstanceName());

        $this->assertInstanceOf('DateTime', $result[0]->getTime()->getNormal());
        $this->assertEquals('2013-03-14 21:19:53.000000', $result[0]->getTime()->getNormal()->format('Y-m-d H:i:s.u'));

        $this->assertEquals(null, $result[0]->getTime()->getHighResolution());
        $this->assertEquals(10, $result[0]->getInterval()->getNormal());
        $this->assertEquals(null, $result[0]->getInterval()->getHighResolution());

        // Check the value[0] values part
        $this->assertCount(1, $result[0]->getValues());

        foreach ($result[0]->getValues() as $value) {
            $this->assertInstanceOf('Aviogram\CollectD\Parser\Entity\Value', $value);
        }

        $this->assertEquals(true, $result[0]->getValues()[0]->isGauge());
        $this->assertEquals(false, $result[0]->getValues()[0]->isAbsolute());
        $this->assertEquals(false, $result[0]->getValues()[0]->isCounter());
        $this->assertEquals(false, $result[0]->getValues()[0]->isDerive());
        $this->assertEquals(1048969216, $result[0]->getValues()[0]->getValue());


        // Check second part
        $this->assertEquals('laptop.lan', $result[1]->getHost());
        $this->assertEquals('interface', $result[1]->getPlugin()->getName());
        $this->assertEquals('lo0', $result[1]->getPlugin()->getInstanceName());
        $this->assertEquals('if_octets', $result[1]->getType()->getName());
        $this->assertEquals(null, $result[1]->getType()->getInstanceName());

        $this->assertInstanceOf('DateTime', $result[1]->getTime()->getNormal());
        $this->assertEquals('2013-03-14 21:19:54.000000', $result[1]->getTime()->getNormal()->format('Y-m-d H:i:s.u'));

        $this->assertEquals(null, $result[1]->getTime()->getHighResolution());
        $this->assertEquals(10, $result[1]->getInterval()->getNormal());
        $this->assertEquals(null, $result[1]->getInterval()->getHighResolution());

        // Check the value[0] values part
        $this->assertCount(2, $result[1]->getValues());

        foreach ($result[1]->getValues() as $value) {
            $this->assertInstanceOf('Aviogram\CollectD\Parser\Entity\Value', $value);
        }

        $this->assertEquals(false, $result[1]->getValues()[0]->isGauge());
        $this->assertEquals(false, $result[1]->getValues()[0]->isAbsolute());
        $this->assertEquals(false, $result[1]->getValues()[0]->isCounter());
        $this->assertEquals(true, $result[1]->getValues()[0]->isDerive());
        $this->assertEquals(8914827, $result[1]->getValues()[0]->getValue());

        $this->assertEquals(false, $result[1]->getValues()[1]->isGauge());
        $this->assertEquals(false, $result[1]->getValues()[1]->isAbsolute());
        $this->assertEquals(false, $result[1]->getValues()[1]->isCounter());
        $this->assertEquals(true, $result[1]->getValues()[1]->isDerive());
        $this->assertEquals(8914828, $result[1]->getValues()[1]->getValue());

        // Check third part
        $this->assertEquals('laptop.lan', $result[2]->getHost());
        $this->assertEquals('interface', $result[2]->getPlugin()->getName());
        $this->assertEquals('lo0', $result[2]->getPlugin()->getInstanceName());
        $this->assertEquals('if_packets', $result[2]->getType()->getName());
        $this->assertEquals(null, $result[2]->getType()->getInstanceName());

        $this->assertInstanceOf('DateTime', $result[2]->getTime()->getNormal());
        $this->assertEquals('2013-03-14 21:19:54.000000', $result[2]->getTime()->getNormal()->format('Y-m-d H:i:s.u'));

        $this->assertEquals(null, $result[2]->getTime()->getHighResolution());
        $this->assertEquals(10, $result[2]->getInterval()->getNormal());
        $this->assertEquals(null, $result[2]->getInterval()->getHighResolution());

        // Check the value[0] values part
        $this->assertCount(2, $result[2]->getValues());

        foreach ($result[2]->getValues() as $value) {
            $this->assertInstanceOf('Aviogram\CollectD\Parser\Entity\Value', $value);
        }

        $this->assertEquals(false, $result[2]->getValues()[0]->isGauge());
        $this->assertEquals(false, $result[2]->getValues()[0]->isAbsolute());
        $this->assertEquals(false, $result[2]->getValues()[0]->isCounter());
        $this->assertEquals(true, $result[2]->getValues()[0]->isDerive());
        $this->assertEquals(0, $result[2]->getValues()[0]->getValue());

        $this->assertEquals(false, $result[2]->getValues()[1]->isGauge());
        $this->assertEquals(false, $result[2]->getValues()[1]->isAbsolute());
        $this->assertEquals(false, $result[2]->getValues()[1]->isCounter());
        $this->assertEquals(true, $result[2]->getValues()[1]->isDerive());
        $this->assertEquals(0, $result[2]->getValues()[1]->getValue());
    }

    public function testNotificationPacket()
    {
        $hex = '0000000f6c6170746f702e6c616e00' .  // Hostname: laptop.lan
            '0100000b6d656d6f727900';              // Signature: memory

        $result = $this->parser->parse(hex2bin($hex));
        $this->assertCount(1, $result);
        $this->assertEquals('memory', $result[0]->getMessage());
    }

    public function testUnsupportedSignaturePacket()
    {
        // Test unsupported signature packet
        $this->setExpectedException('Aviogram\CollectD\Parser\Exception\UnSupported', 'Signature packet', 1);
        $hex = '0000000f6c6170746f702e6c616e00' .   // Hostname: laptop.lan
               '0200000b6d656d6f727900';            // Signature: memory
        $this->parser->parse(hex2bin($hex));
    }

    public function testUnSupportedEncryptedPacket()
    {
        // Test unsupported encryption packet
        $this->setExpectedException('Aviogram\CollectD\Parser\Exception\UnSupported', 'Encrypted packet', 2);
        $hex = '0000000f6c6170746f702e6c616e00' .   // Hostname: laptop.lan
               '0210000b6d656d6f727900';            // Encryption: memory
        $this->parser->parse(hex2bin($hex));
    }

    public function testUnSupportedUnknownPacket()
    {
        // Test unsupported encryption packet
        $this->setExpectedException('Aviogram\CollectD\Parser\Exception\UnSupported', 'Unknown packet', 3);
        $hex = '0000000f6c6170746f702e6c616e00' .   // Hostname: laptop.lan
               '9999000b6d656d6f727900';               // Unknown: memory
        $this->parser->parse(hex2bin($hex));
    }

    public function testUnSupportedUnknownValueType()
    {
        // Test unsupported encryption packet
        $this->setExpectedException('Aviogram\CollectD\Parser\Exception\UnSupported', 'Unknown value type', 4);
        $hex = '0000000f6c6170746f702e6c616e00' .   // Hostname: laptop.lan
            '0006000f001101000000000043cf41';    // Value
        $this->parser->parse(hex2bin($hex));
    }
}
