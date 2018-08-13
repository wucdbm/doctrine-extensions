<?php

use Wucdbm\PhpCsFixer\Config\ConfigFactory;

$copyright = <<<COMMENT
This file is part of the wucdbm/doctrine-extensions package.

Copyright (c) Martin Kirilov <martin@forci.com>

Author Martin Kirilov <martin@forci.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
COMMENT;

return ConfigFactory::createCopyrightedConfig([
    __DIR__ . '/src',
    __DIR__ . '/tests',
], $copyright)
    ->setUsingCache(true);