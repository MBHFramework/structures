<?php namespace Mbh\Collection\Traits\Sequenceable;

use Mbh\Collection\Interfaces\Collection as CollectionInterface;
use Mbh\Collection\Interfaces\Sequenceable as SequenceableInterface;
use Mbh\Collection\FixedArray;
use Mbh\Collection\CallbackHeap;
use Mbh\Iterator\SliceIterator;
use Mbh\Iterator\ConcatIterator;
use Mbh\Collection\Traits\Builder;
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

trait Arrayed
{
    use Arrayed\Countable;
    use Arrayed\ArrayAccess;
    use Arrayed\Iterator;
    use Builder;

    /**
     * @var integer internal capacity
     */
    protected $capacity = self::MIN_CAPACITY;

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
    public function first()
    {
        $this->emptyGuard(__METHOD__);
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

        $splice = $this->splice($index, 0, $values);
        $this->clear();

        $this->pushAll($splice);
    }

    /**
     * @inheritDoc
     */
    public function last()
    {
        $this->emptyGuard(__METHOD__);
        return $this[$this->count() - 1];
    }

    /**
     * Converts negative or large rotations into the minimum positive number
     * of rotations required to rotate the sequence by a given $r.
     */
    private function normalizeRotations(int $r)
    {
        $n = $this->count();

        if ($n < 2) {
            return 0;
        }

        if ($r < 0) {
            return $n - (abs($r) % $n);
        }

        return $r % $n;
    }

    /**
     * @inheritDoc
     */
    public function pop()
    {
        $this->emptyGuard(__METHOD__);
        $value = $this->last();
        $count = $this->count();
        unset($this[--$count]);
        $this->setSize($count);

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
        $splice = $this->splice($index + 1, 1, null);
        $this->clear();

        $this->pushAll($splice);
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

    /**
     * @inheritDoc
     */
    public function shift()
    {
        $this->emptyGuard(__METHOD__);
        $value = $this->first();
        unset($this[0]);

        $this->checkCapacity();

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->sfa->toArray();
    }

    /**
     * @inheritDoc
     */
    public function unshift(...$values)
    {
        $this->insert(0, ...$values);

        return $this->count();
    }

    /**
     * @inheritDoc
     */
    protected function validIndex(int $index)
    {
        return $index >= 0 && $index < $this->count();
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        $this->sfa->setSize(0);
        $this->capacity = self::MIN_CAPACITY;
    }

    protected function getValues(): Traversable
    {
        return $this->sfa;
    }

    protected function setValues(Traversable $traversable)
    {
        $this->clear();
        $this->pushAll($traversable);
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

    public function __clone()
    {
        $this->sfa = SplFixedArray::fromArray($this->toArray());
    }

    abstract protected function checkCapacity();

    abstract protected function emptyGuard($method);

    abstract public function isEmpty(): bool;

    abstract public function splice(int $begin = 0, int $length = null, $replacement = []);
}
