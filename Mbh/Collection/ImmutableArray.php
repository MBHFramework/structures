<?php namespace Mbh\Collection;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\Interfaces\Sequenceable as SequenceableInterface;
use Mbh\Collection\Exceptions\ImmutableException;

/**
 * The Immutable Array
 *
 * This provides special methods for quickly creating an immutable array,
 * either from any Traversable, or using a C-optimized fromArray() to directly
 * instantiate from. Also includes methods fundamental to functional
 * programming, e.g. map, filter, join, and sort.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */

class ImmutableArray extends FixedArray
{
    /**
     * @inheritDoc
     */
    public function clear()
    {
        throw ImmutableException::cannotModify(__CLASS__, __METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function insert(int $index, ...$values)
    {
        throw ImmutableException::cannotModify(__CLASS__, __METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        throw ImmutableException::cannotModify(__CLASS__, __METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        throw ImmutableException::cannotModify(__CLASS__, __METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function pop()
    {
        throw ImmutableException::cannotModify(__CLASS__, __METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function push(...$values)
    {
        throw ImmutableException::cannotModify(__CLASS__, __METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function remove(int $index)
    {
        throw ImmutableException::cannotModify(__CLASS__, __METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function set(int $index, $value)
    {
        throw ImmutableException::cannotModify(__CLASS__, __METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function shift()
    {
        throw ImmutableException::cannotModify(__CLASS__, __METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function sort(callable $callback = null): SequenceableInterface
    {
        throw ImmutableException::cannotModify(__CLASS__, __METHOD__);
    }

    public function unserialize($values)
    {
        throw ImmutableException::cannotModify(__CLASS__, __METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function unshift(...$values)
    {
        throw ImmutableException::cannotModify(__CLASS__, __METHOD__);
    }
}
