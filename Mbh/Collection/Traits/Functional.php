<?php namespace Mbh\Collection\Traits;

use Mbh\Collection\Interfaces\Collection as CollectionInterface;
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

trait Functional
{
    use Functional\Sort {
        Functional\Sort::heapSort as heapSortWithCallback;
        Functional\Sort::heapSorted as heapSortedWithCallback;
    }

    protected function getSplFixedArrayAndSize()
    {
        $count = $this->count();
        return [new SplFixedArray($count), $count];
    }

    /**
     * @inheritDoc
     */
    public function any(callable $callback)
    {
        for ($i = 0; $i < $count; $i++) {
            $this[$i] = $callback($this[$i], $i, $this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function concat(...$args)
    {
        array_unshift($args, $this);

        // Concat this iterator, and variadic args
        $class = new ReflectionClass('Mbh\Iterator\ConcatIterator');
        $concatIt = $class->newInstanceArgs($args);

        // Create as new immutable's iterator
        return static::fromArray($concatIt->toArray());
    }

    /**
     * @inheritDoc
     */
    public function find(callable $callback)
    {
        foreach ($this as $i => $elem) {
            if ($callback($elem, $i, $this)) {
                return $elem;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function filter(callable $callback)
    {
        list($sfa, $count) = $this->getSplFixedArrayAndSize();

        $newCount = 0;
        foreach ($this as $elem) {
            if ($callback($elem)) {
                $sfa[$newCount++] = $elem;
            }
        }

        $sfa->setSize($newCount);
        return new static($sfa);
    }

    /**
     * @inheritDoc
     */
    public function heapSorted(SplHeap $heap)
    {
        return $this->copy()->heapSort($heap);
    }

    /**
     * @inheritDoc
     */
    public function heapSort(SplHeap $heap)
    {
        foreach ($this as $item) {
            $heap->insert($item);
        }

        $this->setSfa(static::fromItems($heap));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function join(string $token = ',', string $secondToken = null): string
    {
        $str = "";
        if ($secondToken !== null) {
            foreach ($this as $i => $elem) {
                $str .= $token . (string) $elem . $secondToken;
            }
        } else {
            $this->rewind();
            while ($this->valid()) {
                $str .= (string) $this->current();
                $this->next();
                if ($this->valid()) {
                    $str .= $token;
                }
            }
        }

        return $str;
    }

    /**
     * @inheritDoc
     */
    public function map(callable $callback)
    {
        list($sfa, $count) = $this->getSplFixedArrayAndSize();

        for ($i = 0; $i < $count; $i++) {
            $sfa[$i] = $callback($this[$i], $i, $this);
        }

        return new static($sfa);
    }

    /**
     * @inheritDoc
     */
    public function reduce(callable $callback, $accumulator = null)
    {
        foreach ($this as $i => $elem) {
            $accumulator = $callback($accumulator, $elem, $i, $this);
        }

        return $accumulator;
    }

    /**
     * @inheritDoc
     */
    public function search($value)
    {
        foreach ($this as $i => $elem) {
            if ($value === $elem) {
                return $i;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function slice(int $begin = 0, int $length = null)
    {
        $end = $begin + $length;
        $it = new SliceIterator($this->getSfa(), $begin, $end);
        return static::fromArray($it->toArray());
    }

    /**
     * @inheritDoc
     */
    public function splice(int $begin = 0, int $length = null, $replacement = [])
    {
        $input = $this->toArray();
        array_splice($input, $begin, $length, $replacement);
        return static::fromArray($input);
    }

    /**
     * @inheritDoc
     */
    public function sort(callable $callback = null)
    {
        if ($callback) {
            return $this->mergeSort($callback);
        }

        return $this->arraySort();
    }

    /**
     * @inheritDoc
     */
    public function sorted(callable $callback = null)
    {
        $copy = FixedArray::fromItems($this->copy());

        if ($callback) {
            $copy->mergeSort($callback);
        }

        $copy->arraySort();

        return static::fromItems($copy);
    }

    /**
     * @inheritDoc
     */
    public function walk(callable $callback)
    {
        foreach ($this as $i => $elem) {
            $callback($elem, $i, $this);
        }

        return $this;
    }

    abstract public static function fromItems(Traversable $array);

    abstract protected function getSfa(): Traversable;

    abstract public function count(): int;

    abstract public function current();

    abstract public function next();

    abstract public function rewind();

    abstract public function valid();
}
