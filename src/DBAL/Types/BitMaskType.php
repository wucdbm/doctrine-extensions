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

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\PhpIntegerMappingType;
use Doctrine\DBAL\Types\Type;

/**
 * https://dev.mysql.com/doc/refman/5.5/en/data-size.html.
 *
 * Table Columns
 *
 * Use the most efficient (smallest) data types possible. MySQL has many specialized types that save disk space and memory. For example, use the smaller integer types if possible to get smaller tables. MEDIUMINT is often a better choice than INT because a MEDIUMINT column uses 25% less space.
 *
 * Declare columns to be NOT NULL if possible. It makes SQL operations faster, by enabling better use of indexes and eliminating overhead for testing whether each value is NULL. You also save some storage space, one bit per column. If you really need NULL values in your tables, use them. Just avoid the default setting that allows NULL values in every column.
 */
abstract class BitMaskType extends Type implements PhpIntegerMappingType {

    // up to 65535
    const TYPE_SMALLINT = 'SMALLINT';
    // up to 4294967295
    const TYPE_INT = 'INT';
    // up to 2^64-1
    const TYPE_BIGINT = 'BIGINT';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $this->validateValueClass();
        // Tip: To avoid Doctrine constantly re-generating your field definition, always add this
        // options={"unsigned"=true} to your field mapping
        // /**
        //  * @ORM\Column(name="some_field", type="your_field_name", options={"unsigned"=true})
        //  */
        // protected $someField;
        $column['unsigned'] = true;
        $column['notnull'] = true;
        // $fieldDeclaration['default'] is not needed as we'd return 0 if no BitMask object @ field
        // $fieldDeclaration['default'] = 0;

        if (isset($column['fieldType'])) {
            switch ($column['fieldType']) {
                case self::TYPE_SMALLINT:
                    return $platform->getSmallIntTypeDeclarationSQL($column);
                case self::TYPE_INT:
                    return $platform->getIntegerTypeDeclarationSQL($column);
                case self::TYPE_BIGINT:
                    return $platform->getBigIntTypeDeclarationSQL($column);
                default:
                    throw new \RuntimeException(sprintf('Field Type %s not supported', $column['fieldType']));
            }
        }

        return $platform->getSmallIntTypeDeclarationSQL($column);
    }

    public function getBindingType(): int
    {
        return ParameterType::INTEGER;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        if ($value instanceof BitMask) {
            return $value->getBits();
        }

        return 0;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform) {
        $class = $this->getValueClass();

        if (null === $value || !is_numeric($value)) {
            return new $class(0);
        }

        return new $class((int)$value);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    abstract protected function getValueClass(): string;

    private function validateValueClass(): void
    {
        if (!is_subclass_of($this->getValueClass(), BitMask::class)) {
            throw new \RuntimeException(sprintf(
                'Return value of %s::%s must be a subclass of %s',
                self::class,
                'getValueClass',
                BitMask::class
            ));
        }
    }
}
