<?php namespace Mbh\Collection\Traits\Sequenceable;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Traversable;
use OutOfRangeException;

trait ArrayAccess
{
    protected $sfa = null;

    /**
     * Create an fixed array
     *
     * @param Traversable $array data
     */
    protected function __construct(Traversable $array)
    {
        $this->sfa = $array;
        $this->checkCapacity();
    }

    /**
     * ArrayAccess
     */
    public function offsetExists($offset): bool
    {
        return is_integer($offset)
            && $this->validIndex($offset)
            && $this->sfa->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->sfa->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->push($value);
        } elseif (is_integer($offset)) {
            $this->set($offset, $value);
        }
    }

    public function offsetUnset($offset)
    {
        return is_integer($offset)
            && $this->remove($offset);
    }

    /**
     * Adds zero or more values to the end of the sequence.
     *
     * @param mixed ...$values
     */
    abstract public function push(...$values);

    /**
     * Replaces the value at a given index in the sequence with a new value.
     *
     * @param int   $index
     * @param mixed $value
     *
     * @throws OutOfRangeException if the index is not in the range [0, size-1]
     */
    abstract public function set(int $index, $value);
}
