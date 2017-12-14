<?php namespace Mbh\Collection;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\Interfaces\Collection as CollectionInterface;
use Traversable;
use ArrayAccess;
use IteratorAggregate;
use Error;
use OutOfBoundsException;
use OutOfRangeException;

/**
 * A sequence of unique values.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */
class Set implements ArrayAccess, CollectionInterface, IteratorAggregate
{
    use Traits\Collection;

    const MIN_CAPACITY = Map::MIN_CAPACITY;

    /**
     * @var Map internal map to store the values.
     */
    private $table;

    /**
     * Creates a new set using the values of an array or Traversable object.
     * The keys of either will not be preserved.
     *
     * @param array|Traversable|null $values
     */
    public function __construct($values = null)
    {
        $this->table = new Map();

        if (func_num_args()) {
            $this->add(...$values);
        }
    }

    /**
     * Adds zero or more values to the set.
     *
     * @param mixed ...$values
     */
    public function add(...$values)
    {
        foreach ($values as $value) {
            $this->table->put($value, null);
        }
    }

    /**
     * Ensures that enough memory is allocated for a specified capacity. This
     * potentially reduces the number of reallocations as the size increases.
     *
     * @param int $capacity The number of values for which capacity should be
     *                      allocated. Capacity will stay the same if this value
     *                      is less than or equal to the current capacity.
     */
    public function allocate(int $capacity)
    {
        $this->table->allocate($capacity);
    }

    /**
     * Returns the current capacity of the set.
     *
     * @return int
     */
    public function capacity(): int
    {
        return $this->table->capacity();
    }

    /**
     * Clear all elements in the Set
     */
    public function clear()
    {
        $this->table->clear();
    }

    /**
     * Determines whether the set contains all of zero or more values.
     *
     * @param mixed ...$values
     *
     * @return bool true if at least one value was provided and the set
     *              contains all given values, false otherwise.
     */
    public function contains(...$values): bool
    {
        foreach ($values as $value) {
            if (!$this->table->hasKey($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function copy()
    {
        return new self($this);
    }

    /**
     * Returns the number of elements in the Stack
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->table);
    }

    /**
     * Returns the first value in the set.
     *
     * @return mixed the first value in the set.
     */
    public function first()
    {
        return $this->table->first()->key;
    }

    /**
     * Returns the value at a specified position in the set.
     *
     * @param int $position
     *
     * @return mixed|null
     *
     * @throws OutOfRangeException
     */
    public function get(int $position)
    {
        return $this->table->skip($position)->key;
    }

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        return $this->table->isEmpty();
    }

    /**
     * Returns the last value in the set.
     *
     * @return mixed the last value in the set.
     */
    public function last()
    {
        return $this->table->last()->key;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    /**
     * Get iterator
     */
    public function getIterator()
    {
        foreach ($this->table as $key => $value) {
            yield $key;
        }
    }

    /**
     * @inheritdoc
     *
     * @throws OutOfBoundsException
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->add($value);
            return;
        }

        throw new OutOfBoundsException();
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->table->skip($offset)->key;
    }

    /**
     * @inheritdoc
     *
     * @throws Error
     */
    public function offsetExists($offset)
    {
        throw new Error();
    }

    /**
     * @inheritdoc
     *
     * @throws Error
     */
    public function offsetUnset($offset)
    {
        throw new Error();
    }
}
