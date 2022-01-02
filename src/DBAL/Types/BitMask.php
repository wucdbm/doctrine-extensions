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

/**
 * Example    Name                  Result
 * $a & $b    And                  Bits that are set in both $a and $b are set.
 * $a | $b    Or (inclusive or)    Bits that are set in either $a or $b are set.
 * $a ^ $b    Xor (exclusive or)   Bits that are set in $a or $b but not both are set.
 * ~ $a       Not                  Bits that are set in $a are not set, and vice versa.
 * $a << $b   Shift left           Shift the bits of $a $b steps to the left (each step means "multiply by two")
 * $a >> $b   Shift right          Shift the bits of $a $b steps to the right (each step means "divide by two").
 */
abstract class BitMask {

    protected const FLAG_NONE = 0;

    /** @var int */
    private $bits;

    final public function __construct(int $bits) {
        $this->bits = $bits;
    }

    /**
     * @param int $bits
     *
     * @return $this
     */
    protected function set(int $bits): static {
        $this->bits |= $bits;

        return $this;
    }

    /**
     * @param int $bits
     *
     * @return $this
     */
    protected function unset(int $bits): static {
        $this->bits &= ~$bits;

        return $this;
    }

    /**
     * @param int $bits
     *
     * @return $this
     */
    protected function toggle(int $bits): static {
        $this->bits ^= $bits;

        return $this;
    }

    protected function flip(int $bits, bool $setOrUnset): static {
        if ($setOrUnset) {
            $this->set($bits);
        } else {
            $this->unset($bits);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function reset(): static {
        $this->bits = self::FLAG_NONE;

        return $this;
    }

    protected function check(int $bits): bool {
        return $this->bits & $bits;
    }

    public function getBits(): int {
        return $this->bits;
    }

    /**
     * @param int $bits
     *
     * @return $this
     */
    public function setBits(int $bits): static {
        $this->bits = $bits;

        return $this;
    }
}
