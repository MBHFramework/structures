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

    public function toArray(): array
    {
        return $this->sfa->toArray();
    }

    protected function validIndex(int $index)
    {
        return $index >= 0 && $index < count($this);
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
        } else {
            return is_integer($offset)
            && $this->validIndex($offset)
            && $this->sfa->offsetSet($offset, $value);
        }
    }

    public function offsetUnset($offset)
    {
        return is_integer($offset)
            && $this->validIndex($offset)
            && $this->sfa->offsetUnset($offset);
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
     * @inheritDoc
     */
    public static function fromItems(Traversable $array)
    {
        // We can only do it this way if we can count it
        if ($array instanceof Countable) {
            $sfa = new SplFixedArray(count($array));

            foreach ($array as $i => $elem) {
                $sfa[$i] = $elem;
            }

            return new static($sfa);
        }

        // If we can't count it, it's simplest to iterate into an array first
        return static::fromArray(iterator_to_array($array));
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
    public function copy()
    {
        return static::fromArray($this->toArray());
    }

    /**
     * @inheritDoc
     */
    public function contains(...$values): bool
    {
        foreach ($values as $value) {
            if (!$this->find($value)) {
                return false;
            }
        }

        return true;
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
        if (! $this->validIndex($index)) {
            throw new OutOfRangeException();
        }

        return $this[$index];
    }

    /**
     * @inheritDoc
     */
    public function insert(int $index, ...$values)
    {
        if (! $this->validIndex($index) && $index !== count($this)) {
            throw new OutOfRangeException();
        }
    }

    /**
     * @inheritDoc
     */
    public function last()
    {
        if ($this->isEmpty()) {
            throw new UnderflowException();
        }

        return $this[count($this) - 1];
    }

    /**
     * Pushes all values of either an array or traversable object.
     */
    private function pushAll($values)
    {
        foreach ($values as $value) {
            $this->sfa->setSize(count($this) + 1);
            $this->sfa[count($this) - 1] = $value;
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

    abstract protected function checkCapacity();
}
