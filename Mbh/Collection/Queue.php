<?php namespace Mbh\Collection;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\Interfaces\Collection as CollectionInterface;
use Mbh\Interfaces\Allocated as AllocatedInterface;
use Traversable;
use ArrayAccess;
use IteratorAggregate;
use Error;
use OutOfBoundsException;

/**
 * A “first in, first out” or “FIFO” collection that only allows access to the
 * value at the front of the queue and iterates in that order, destructively.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */

class Queue implements AllocatedInterface, ArrayAccess, CollectionInterface, IteratorAggregate
{
    use Traits\Collection;

    const MIN_CAPACITY = 8;

    /**
     * @var Deque internal deque to store values.
     */
    protected $deque;

    /**
     * Creates an instance using the values of an array or Traversable object.
     *
     * @param array|Traversable $values
     */
    public function __construct($values = [])
    {
        $this->deque = Deque::empty();

        $this->pushAll($values);
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
        $this->deque->allocate($capacity);
    }

    /**
     * Returns the current capacity of the queue.
     *
     * @return int
     */
    public function capacity(): int
    {
        return $this->deque->capacity();
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        $this->deque->clear();
    }

    /**
     * @inheritDoc
     */
    public function copy()
    {
        return new self($this->deque);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->deque);
    }

    /**
     * Returns the value at the front of the queue without removing it.
     *
     * @return
     */
    public function peek()
    {
        return $this->deque->first();
    }

    /**
     * Returns and removes the value at the front of the Queue.
     *
     * @return mixed
     */
    public function pop()
    {
        return $this->deque->shift();
    }

    /**
     * Pushes zero or more values into the front of the queue.
     *
     * @param mixed ...$values
     */
    public function push(...$values)
    {
        $this->deque->push(...$values);
    }

    /**
     * Creates associations for all keys and corresponding values of either an
     * array or iterable object.
     *
     * @param Traversable|array $values
     */
    protected function pushAll($values)
    {
        foreach ($values as &$value) {
            $this[] = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->deque->toArray();
    }

    /**
     * @inheritDoc
     */
    public function unserialize($values)
    {
        $values = unserialize($values);
        $this->deque = Deque::fromArray($values);
    }

    /**
     *
     */
    public function getIterator()
    {
        while (!$this->isEmpty()) {
            yield $this->pop();
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
            $this->push($value);
        } else {
            throw new OutOfBoundsException();
        }
    }

    /**
     * @inheritdoc
     *
     * @throws Error
     */
    public function offsetGet($offset)
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

    /**
     * @inheritdoc
     *
     * @throws Error
     */
    public function offsetExists($offset)
    {
        throw new Error();
    }
}
