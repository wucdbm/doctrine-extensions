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

use Wucdbm\DoctrineExtensions\DBAL\Types\BitMaskType;

class CustomBitMaskType extends BitMaskType {

    protected function getValueClass(): string {
        return CustomBitMask::class;
    }

    public function getName() {
        return 'custom_bitmask_type';
    }
}
