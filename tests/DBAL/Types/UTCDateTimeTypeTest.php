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
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use Wucdbm\DoctrineExtensions\DBAL\Types\UTCDateTimeType;

class UTCDateTimeTypeTest extends TestCase {

    /** @var MySqlPlatform */
    protected $platform;

    /** @var UTCDateTimeType */
    protected $instance;

    protected function setUp() {
        if (!Type::hasType('utc_datetime')) {
            Type::addType('utc_datetime', UTCDateTimeType::class);
        }
        $this->platform = new MySqlPlatform();
        $this->instance = Type::getType('utc_datetime');
        parent::setUp();
    }

    /**
     * @dataProvider testConvertToDatabaseValueDataProvider
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testConvertToDatabaseValue($value, ?\DateTime $expected, $expectedException) {
        if ($expectedException) {
            $this->expectException($expectedException);
        }

        $dateTimeValue = $dateTimeZone = null;

        if ($value instanceof \DateTime) {
            // If passed value is \DateTime, then once serialized
            // It should have the same value and time zone after conversion
            $dateTimeValue = $value->format('Y-m-d H:i:s');
            $dateTimeZone = $value->getTimezone()->getName();
        }

        $return = $this->instance->convertToDatabaseValue($value, $this->platform);

        $this->assertEquals($expected instanceof \DateTime ? $expected->format('Y-m-d H:i:s') : null, $return);

        if ($dateTimeValue && $dateTimeZone) {
            $dateTimeValueNew = $value->format('Y-m-d H:i:s');
            $dateTimeZoneNew = $value->getTimezone()->getName();

            $this->assertEquals($dateTimeValue, $dateTimeValueNew);
            $this->assertEquals($dateTimeZone, $dateTimeZoneNew);
        }
    }

    public function testConvertToDatabaseValueDataProvider() {
        return [
            'NULL should be serialized into a NULL' => [
                null, null, null
            ],
            'NOT \DateTime should throw an Exception' => [
                26, null, ConversionException::class
            ],
            '\DateTime in UTC should return the same Y-m-d H:i:s value' => [
                new \DateTime('2025-10-12 12:00:00', new \DateTimeZone('UTC')), new \DateTime('2025-10-12 12:00:00', new \DateTimeZone('UTC')), null
            ],
            '\DateTime in Europe/Moscow should return the same Y-m-d H:i:s value' => [
                new \DateTime('2025-10-12 15:00:00', new \DateTimeZone('Europe/Moscow')), new \DateTime('2025-10-12 12:00:00', new \DateTimeZone('UTC')), null
            ],
        ];
    }

    /**
     * @dataProvider testConvertToPHPValueDataProvider
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testConvertToPHPValue($value, ?string $expectedDate, ?string $expectedTimeZone, $expectedException) {
        if ($expectedException) {
            $this->expectException($expectedException);
        }

        /** @var \DateTime|null $return */
        $return = $this->instance->convertToPHPValue($value, $this->platform);

        if ($expectedDate && $expectedTimeZone) {
            $this->assertInstanceOf(\DateTIme::class, $return);
            $this->assertEquals($expectedDate, $return->format('Y-m-d H:i:s'));
            $this->assertEquals($expectedTimeZone, $return->getTimezone()->getName());
        } else {
            $this->assertNull($return);
        }
    }

    public function testConvertToPHPValueDataProvider() {
        return [
            'NULL value should return NULL' => [
                null, null, null, null
            ],
            '\DateTime value should return exactly the same \DateTime' => [
                new \DateTime('2025-10-12 12:00:00', new \DateTimeZone('Europe/Moscow')), '2025-10-12 12:00:00', 'Europe/Moscow', null
            ],
            'Proper date value should return a \DateTime in UTC' => [
                '2025-10-12 12:00:00', '2025-10-12 12:00:00', 'UTC', null
            ],
            'Invalid \DateTime should throw Exception' => [
                '2025-13-36 36_00_00', '2025-10-12 12:00:00', 'UTC', ConversionException::class
            ],
        ];
    }
}
