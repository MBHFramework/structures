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
    }

    /**
     * Factory for building FixedArrays from any traversable
     *
     * @return SequenceableInterface
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
     * Build from an array
     *
     * @return SequenceableInterface
     */
    public static function fromArray(array $array): SequenceableInterface
    {
        return new static(SplFixedArray::fromArray($array));
    }

    /**
     * Creates a shallow copy of the collection.
     *
     * @return CollectionInterface a shallow copy of the collection.
     */
    public function copy(): CollectionInterface
    {
        return static::fromArray($this->toArray());
    }

    /**
     * Map elements to a new Sequenceable via a callback
     *
     * @param callable $callback Function to map new data
     * @return SequenceableInterface
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
     * forEach, or "walk" the data
     * Exists primarily to provide a consistent interface, though it's seldom
     * any better than a simple php foreach. Mainly useful for chaining.
     * Named walk for historic reasons - forEach is reserved in PHP
     *
     * @param callable $callback Function to call on each element
     * @return SequenceableInterface
     */
    public function walk(callable $callback): SequenceableInterface
    {
        foreach ($this as $i => $elem) {
            $callback($elem, $i, $this);
        }

        return $this;
    }

    /**
     * Filter out elements
     *
     * @param callable $callback Function to filter out on false
     * @return SequenceableInterface
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
     * Reduce to a single value
     *
     * @param callable $callback Callback(
     *     mixed $previous, mixed $current[, mixed $index, mixed $immArray]
     * ):mixed Callback to run reducing function
     * @param mixed $accumulator Initial value for first argument
     */
    public function reduce(callable $callback, $accumulator = null)
    {
        foreach ($this as $i => $elem) {
            $accumulator = $callback($accumulator, $elem, $i, $this);
        }

        return $accumulator;
    }

    /**
     * Join a set of strings together.
     *
     * @param string $token Main token to put between elements
     * @param string $secondToken If set, $token on left $secondToken on right
     * @return string
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
     * Take a slice of the array
     *
     * @param int $begin Start index of slice
     * @param int $end End index of slice
     * @return SequenceableInterface
     */
    public function slice(int $begin = 0, int $end = null): SequenceableInterface
    {
        $it = new SliceIterator($this, $begin, $end);
        return new static($it);
    }

    /**
     * Concat to the end of this array
     *
     * @param Traversable,...
     * @return SequenceableInterface
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
     * Find a single element
     *
     * @param callable $callback The test to run on each element
     * @return mixed The element we found
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
     * Sorts the collection
     *
     * @param callable $callback The sort callback
     * @return SequenceableInterface
     */
    public function sort(callable $callback = null): SequenceableInterface
    {
        if ($callback) {
            return $this->mergeSort($callback);
        }

        return $this->arraySort();
    }

    /**
     * Return a new sorted Sequenceable
     *
     * @param callable $callback The sort callback
     * @return SequenceableInterface
     */
    public function sorted(callable $callback = null): SequenceableInterface
    {
        $copy = FixedArray::fromArray($this->toArray());

        if ($callback) {
            $copy->mergeSort($callback);
        }

        $copy->arraySort();

        return new static($copy);
    }

    /**
     * Sort a new Sequenceable by filtering through a heap.
     * Tends to run much faster than array or merge sorts, since you're only
     * sorting the pointers, and the sort function is running in a highly
     * optimized space.
     *
     * @param SplHeap $heap The heap to run for sorting
     * @return SequenceableInterface
     */
    public function heapSort(SplHeap $heap): SequenceableInterface
    {
        foreach ($this as $item) {
            $heap->insert($item);
        }
        return static::fromItems($heap);
    }
}
