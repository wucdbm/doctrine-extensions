<?php

/*
 * This file is part of the wucdbm/doctrine-extensions package.
 *
 * Copyright (c) Martin Kirilov <martin@forci.com>
 *
 * Author Martin Kirilov <martin@forci.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Wucdbm\DoctrineExtensions\Tests\DBAL\Types;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use Wucdbm\DoctrineExtensions\DBAL\Types\BitMask;
use Wucdbm\DoctrineExtensions\DBAL\Types\BitMaskType;

class BitMaskTypeTest extends TestCase {

    /** @var MySqlPlatform */
    protected $platform;

    /** @var BitMaskType */
    protected $instance;

    protected function setUp() {
        if (!Type::hasType('custom_bitmask_type')) {
            Type::addType('custom_bitmask_type', CustomBitMaskType::class);
        }
        $this->platform = new MySqlPlatform();
        $this->instance = Type::getType('custom_bitmask_type');
        parent::setUp();
    }

    /**
     * @dataProvider testConvertToDatabaseValueDataProvider
     *
     * @param $value
     * @param $expected
     */
    public function testConvertToDatabaseValue($value, $expected) {
        $return = $this->instance->convertToDatabaseValue($value, $this->platform);
        $this->assertEquals($expected, $return);
    }

    public function testConvertToDatabaseValueDataProvider() {
        return [
            'Non-BitMask (integer) should return 0' => [
                1234, 0
            ],
            'Non-BitMask (string) should return 0' => [
                'testtest', 0
            ],
            'BitMask object (123) should return the bits provided' => [
                new BitMask(123), 123
            ],
            'BitMask object (321) should return the bits provided' => [
                new BitMask(321), 321
            ],
        ];
    }

    /**
     * @dataProvider testConvertToPHPValueDataProvider
     *
     * @param $value
     * @param $expectedBits
     * @param $expectedClass
     */
    public function testConvertToPHPValue($value, $expectedBits, $expectedClass) {
        /** @var CustomBitMask $return */
        $return = $this->instance->convertToPHPValue($value, $this->platform);

        $this->assertEquals($expectedBits, $return->getBits());
        $this->assertInstanceOf($expectedClass, $return);
    }

    public function testConvertToPHPValueDataProvider() {
        return [
            'NULL should be converted to zero bitmask' => [
                null, 0, CustomBitMask::class
            ],
            'Non-numeric should be converted to zero bitmask' => [
                'asdtest', 0, CustomBitMask::class
            ],
            'Integer should be converted to bitmask with appropriate bits' => [
                123, 123, CustomBitMask::class
            ],
            'Numeric string should be converted to bitmask with appropriate bits' => [
                '1234', 1234, CustomBitMask::class
            ],
        ];
    }
}
