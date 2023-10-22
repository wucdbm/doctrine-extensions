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

namespace Wucdbm\DoctrineExtensions\DBAL\Types;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateType;

class UTCDateType extends DateType {

    /** @var DateTimeZone|null */
    private static $utc;

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof DateTime) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $this->getName(),
                ['null', DateTime::class]
            );
        }

        // TODO Unit test that upon saving anything that uses this, the date time value has the SAME timezone and date since it gets passed around by reference

        // Doing this is VERY IMPORTANT
        // Because otherwise the values of implementing fields
        // Will remain with a different timezone set
        // and the \DateTime::format() function will
        // Will be different and ->format() will yield wrong values
        $timezone = $value->getTimezone();
        $value->setTimezone($this->getUtc());
        $string = $value->format($platform->getDateFormatString());
        $value->setTimezone($timezone);

        return $string;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?DateTime
    {
        if (null === $value || $value instanceof DateTime) {
            return $value;
        }

        $val = DateTime::createFromFormat(sprintf('%s H:i:s', $platform->getDateFormatString()), sprintf('%s 00:00:00', $value), $this->getUtc());

        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $val;
    }

    private function getUtc(): DateTimeZone {
        return self::$utc ?: self::$utc = new DateTimeZone('UTC');
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public function getName(): string
    {
        return 'utc_date';
    }
}
