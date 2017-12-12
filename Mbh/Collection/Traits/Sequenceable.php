<?php namespace Mbh\Collection\Traits;

use Mbh\Collection\Interfaces\Collection as CollectionInterface;
use Mbh\Collection\Interfaces\Sequenceable as SequenceableInterface;
use Mbh\Collection\FixedArray;
use Mbh\Collection\CallbackHeap;
use Mbh\Iterator\SliceIterator;
use Mbh\Iterator\ConcatIterator;
use SplFixedArray;
use SplHeap;
use SplStack;
use LimitIterator;
use Iterator;
use ArrayAccess;
use Countable;
use CallbackFilterIterator;
use JsonSerializable;
use RuntimeException;
use Traversable;
use ReflectionClass;
use UnderflowException;
use OutOfRangeException;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

trait Sequenceable
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
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $array)
    {
        return new static(SplFixedArray::fromArray($array));
    }

    /**
     * @inheritDoc
     */
    public static function fromItems(Traversable $array)
    {
        if (!$array instanceof Countable) {
            return static::fromArray(iterator_to_array($array));
        }

        $sfa = new SplFixedArray(count($array));

        foreach ($array as $i => $elem) {
            $sfa[$i] = $elem;
        }

        return new static($sfa);
    }

    /**
     * @inheritDoc
     */
    public function contains(...$values): bool
    {
        foreach ($values as $value) {
            if ($this->search($value) !== null) {
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
        return static::fromArray($this->toArray());
    }

    /**
     * @inheritDoc
     */
    public function first()
    {
        if ($this->isEmpty()) {
            throw new UnderflowException();
        }

        return $this[0];
    }

    /**
     * @inheritDoc
     */
    public function get(int $index)
    {
        if (!$this->validIndex($index)) {
            throw new OutOfRangeException();
        }

        return $this[$index];
    }

    /**
     * @inheritDoc
     */
    public function insert(int $index, ...$values)
    {
        if (!$this->validIndex($index) && $index !== $this->count()) {
            throw new OutOfRangeException();
        }

        $slice = $this->slice($index, $this->count());
        $this->setSize($index);

        $this->pushAll($values, $slice);
    }

    /**
     * @inheritDoc
     */
    public function last()
    {
        if ($this->isEmpty()) {
            throw new UnderflowException();
        }

        return $this[$this->count() - 1];
    }

    /**
     * @inheritDoc
     */
    public function pop()
    {
        if ($this->isEmpty()) {
            throw new UnderflowException();
        }

        $value = $this->last();
        unset($this[$this->count() - 1]);

        $this->checkCapacity();

        return $value;
    }

    /**
     * Pushes all values of either an array or traversable object.
     */
    private function pushAll(...$args)
    {
        $size = $this->getSize();

        foreach ($args as &$values) {
            $this->setSize($size + count($values));

            foreach ($values as $value) {
                $this[$size++] = $value;
            }
        }

        $this->checkCapacity();
    }

    /**
     * @inheritDoc
     */
    public function push(...$values)
    {
        $this->pushAll($values);
    }

    /**
     * @inheritDoc
     */
    public function remove(int $index)
    {
        if (!$this->validIndex($index)) {
            throw new OutOfRangeException();
        }

        $value = $this[$index];
        $this->sfa->offsetUnset($index);

        $this->checkCapacity();
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function set(int $index, $value)
    {
        if (!$this->validIndex($index)) {
            throw new OutOfRangeException();
        }

        $this->sfa->offsetSet($index, $value);
    }

    public function toArray(): array
    {
        return $this->sfa->toArray();
    }

    protected function validIndex(int $index)
    {
        return $index >= 0 && $index < $this->getSize();
    }

    /**
     * Countable
     */
    public function count(): int
    {
        return count($this->sfa);
    }

    /**
     * Iterator
     */
    public function current()
    {
        return $this->sfa->current();
    }

    public function key(): int
    {
        return $this->sfa->key();
    }

    public function next()
    {
        return $this->sfa->next();
    }

    public function rewind()
    {
        return $this->sfa->rewind();
    }

    public function valid()
    {
        return $this->sfa->valid();
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
            && $this->validIndex($offset)
            && $this->remove($offset);
    }

    public function clear()
    {
        return $this->sfa->clear();
    }

    protected function getMainTraversable(): Traversable
    {
        return $this->sfa;
    }

    protected function setTraversable(Traversable $traversable)
    {
        $this->sfa = $traversable;
    }

    /**
     * Gets the size of the array.
     *
     * @return int
     */
    protected function getSize(): int
    {
        return $this->sfa->getSize();
    }

    /**
     * Change the size of an array to the new size of size.
     * If size is less than the current array size, any values after the
     * new size will be discarded. If size is greater than the current
     * array size, the array will be padded with NULL values.
     *
     * @param int $size The new array size. This should be a value between 0
     * and PHP_INT_MAX.
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    protected function setSize(int $size): bool
    {
        return $this->sfa->setSize($size);
    }

    abstract protected function checkCapacity();

    abstract public function isEmpty(): bool;

    abstract public function search($value);
}
