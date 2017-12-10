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

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

trait Sequenceable
{
    use Collection;
    use Sort {
        Sort::heapSort as heapSortWithCallback;
        Sort::heapSorted as heapSortedWithCallback;
    }

    /**
     * @inheritDoc
     */
    public static function fromItems(Traversable $array): SequenceableInterface
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
    public static function fromArray(array $array): SequenceableInterface
    {
        return new static(SplFixedArray::fromArray($array));
    }

    /**
     * @inheritDoc
     */
    public function copy(): CollectionInterface
    {
        return static::fromArray($this->toArray());
    }

    /**
     * @inheritDoc
     */
    public function map(callable $callback): SequenceableInterface
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
    public function walk(callable $callback): SequenceableInterface
    {
        foreach ($this as $i => $elem) {
            $callback($elem, $i, $this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filter(callable $callback): SequenceableInterface
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
    public function slice(int $begin = 0, int $end = null): SequenceableInterface
    {
        $it = new SliceIterator($this, $begin, $end);
        return new static($it);
    }

    /**
     * @inheritDoc
     */
    public function concat(): SequenceableInterface
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
    public function sort(callable $callback = null): SequenceableInterface
    {
        if ($callback) {
            return $this->mergeSort($callback);
        }

        return $this->arraySort();
    }

    /**
     * @inheritDoc
     */
    public function sorted(callable $callback = null): SequenceableInterface
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
    public function heapSorted(SplHeap $heap): SequenceableInterface
    {
        return $this->copy()->heapSort($heap);
    }

    /**
     * @inheritDoc
     */
    public function heapSort(SplHeap $heap): SequenceableInterface
    {
        foreach ($this as $item) {
            $heap->insert($item);
        }

        $this->setTraversable(static::fromItems($heap));

        return $this;
    }

    abstract protected function setTraversable(Traversable $traversable);
}
