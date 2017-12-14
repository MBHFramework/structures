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
use UnderflowException;

/**
 * A “last in, first out” or “LIFO” collection that only allows access to the
 * value at the top of the structure and iterates in that order, destructively.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */

class Queue implements ArrayAccess, CollectionInterface, IteratorAggregate
{
    use Traits\Collection;

    const MIN_CAPACITY = 8;

    /**
     * @var FixedArray internal sfa to store values of the stack.
     */
    private $sfa;

    /**
     * Creates an instance using the values of an array or Traversable object.
     *
     * @param array|\Traversable $values
     */
    public function __construct($values = null)
    {
        $this->sfa = FixedArray::fromArray([]);

        $this->pushAll($pairs);
    }

    /**
     * Clear all elements in the Stack
     */
    public function clear()
    {
        $this->sfa->clear();
    }

    /**
     * @inheritdoc
     */
    public function copy()
    {
        return new self($this->sfa);
    }

    /**
     * Returns the number of elements in the Stack
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->sfa);
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
        $this->sfa->allocate($capacity);
    }

    /**
     * Returns the current capacity of the stack.
     *
     * @return int
     */
    public function capacity(): int
    {
        return $this->sfa->capacity();
    }

    /**
     * Returns the value at the top of the stack without removing it.
     *
     * @return mixed
     *
     * @throws UnderflowException if the stack is empty.
     */
    public function peek()
    {
        return $this->sfa->last();
    }

    /**
     * Returns and removes the value at the top of the stack.
     *
     * @return mixed
     *
     * @throws UnderflowException if the stack is empty.
     */
    public function pop()
    {
        return $this->sfa->pop();
    }

    /**
     * Pushes zero or more values onto the top of the stack.
     *
     * @param mixed ...$values
     */
    public function push(...$values)
    {
        $this->sfa->push(...$values);
    }

    /**
     * Creates associations for all keys and corresponding values of either an
     * array or iterable object.
     *
     * @param Traversable|array $values
     */
    private function pushAll($values)
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
        return array_reverse($this->sfa->toArray());
    }

    /**
     *
     */
    public function getIterator()
    {
        while (! $this->isEmpty()) {
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
