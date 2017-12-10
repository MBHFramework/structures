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

trait Functional
{
    use Sort {
        Sort::heapSort as heapSortWithCallback;
        Sort::heapSorted as heapSortedWithCallback;
    }

    /**
     * @inheritDoc
     */
    public function map(callable $callback)
    {
        $count = count($this);
        $sfa = new SplFixedArray($count);

        for ($i = 0; $i < $count; $i++) {
            $sfa[$i] = $callback($this[$i], $i, $this);
        }

        return new static($sfa);
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

    /**
     * @inheritDoc
     */
    public function filter(callable $callback)
    {
        $count = count($this);
        $sfa = new SplFixedArray($count);
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
    public function join(string $token = ',', string $secondToken = null): string
    {
        $str = "";
        if ($secondToken) {
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
    public function slice(int $begin = 0, int $end = null)
    {
        $it = new SliceIterator($this, $begin, $end);
        return new static($it);
    }

    /**
     * @inheritDoc
     */
    public function concat()
    {
        $args = func_get_args();
        array_unshift($args, $this);

        // Concat this iterator, and variadic args
        $class = new ReflectionClass('Mbh\Iterator\ConcatIterator');
        $concatIt = $class->newInstanceArgs($args);

        // Create as new immutable's iterator
        return new static($concatIt);
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
        $copy = FixedArray::fromItems($this);

        if ($callback) {
            $copy->mergeSort($callback);
        }

        $copy->arraySort();

        return static::fromItems($copy);
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

        $this->setTraversable(static::fromItems($heap));

        return $this;
    }
}
