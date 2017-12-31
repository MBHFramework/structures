<?php namespace Mbh\Traits;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Exceptions\EmptyException;

/**
 * Common to structures that require a capacity which is a power of two.
 */
trait EmptyGuard
{
    protected function emptyGuard($method)
    {
        if ($this->isEmpty()) {
            throw EmptyException::cannotAccessWhenEmpty(__CLASS__, $method);
        }
    }

    abstract public function isEmpty(): bool;
}
