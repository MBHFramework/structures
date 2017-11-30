<?php namespace Mbh\Collection;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

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
 * The Immutable Array
 *
 * This provides special methods for quickly creating an immutable array,
 * either from any Traversable, or using a C-optimized fromArray() to directly
 * instantiate from. Also includes methods fundamental to functional
 * programming, e.g. map, filter, join, and sort.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */

class ImmutableArray implements Iterator, ArrayAccess, Countable, JsonSerializable
{
    use SortTrait {
        SortTrait::quickSort as quickSortWithCallback;
    }

    // The secondary flash array - fixed array
    private $sfa = null;

    /**
     * Create an immutable array
     *
     * @param Traversable $immute Data guaranteed to be immutable
     */
    private function __construct(Traversable $immute)
    {
        $this->sfa = $immute;
    }

    /**
     * Map elements to a new ImmutableArray via a callback
     *
     * @param callable $callback Function to map new data
     * @return ImmutableArray
     */
    public function map(callable $callback): self
    {
        $count = count($this);
        $sfa = new SplFixedArray($count);

        for ($i = 0; $i < $count; $i++) {
            $sfa[$i] = $callback($this->sfa[$i], $i, $this);
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
     * @return ImmutableArray
     */
    public function walk(callable $callback): self
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
     * @return ImmutableArray
     */
    public function filter(callable $callback): self
    {
        $count = count($this->sfa);
        $sfa = new SplFixedArray($count);
        $newCount = 0;

        foreach ($this->sfa as $elem) {
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
        foreach ($this->sfa as $i => $elem) {
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
            foreach ($this->sfa as $i => $elem) {
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
     * @return ImmutableArray
     */
    public function slice(int $begin = 0, int $end = null): self
    {
        $it = new SliceIterator($this->sfa, $begin, $end);
        return new static($it);
    }

    /**
     * Concat to the end of this array
     *
     * @param Traversable,...
     * @return ImmutableArray
     */
    public function concat(): self
    {
        $args = func_get_args();
        array_unshift($args, $this->sfa);

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
        foreach ($this->sfa as $i => $elem) {
            if ($callback($elem, $i, $this)) {
                return $elem;
            }
        }
    }

    /**
     * Return a new sorted ImmutableArray
     *
     * @param callable $callback The sort callback
     * @return ImmutableArray
     */
    public function sort(callable $callback = null)
    {
        if ($callback) {
            return $this->arraySort($callback);
        }

        return $this->arraySort();
    }

    /**
     * Sort a new ImmutableArray by filtering through a heap.
     * Tends to run much faster than array or merge sorts, since you're only
     * sorting the pointers, and the sort function is running in a highly
     * optimized space.
     *
     * @param SplHeap $heap The heap to run for sorting
     * @return ImmutableArray
     */
    public function heapSort(SplHeap $heap): self
    {
        foreach ($this as $item) {
            $heap->insert($item);
        }
        return static::fromItems($heap);
    }

    /**
     * Fallback behaviour to use the builtin array sort functions
     *
     * @param callable $callback The callback for comparison
     * @return ImmutableArray
     */
    protected function arraySort(callable $callback = null)
    {
        $array = $this->toArray();
        if ($callback) {
            usort($array, $callback);
        } else {
            sort($array);
        }
        return static::fromArray($array);
    }

    /**
     * Factory for building ImmutableArrays from any traversable
     *
     * @return ImmutableArray
     */
    public static function fromItems(Traversable $array): self
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
     * @return ImmutableArray
     */
    public static function fromArray(array $array): self
    {
        return new static(SplFixedArray::fromArray($array));
    }

    public function toArray(): array
    {
        return $this->sfa->toArray();
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
        return $this->sfa->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->sfa->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Attempt to mutate immutable ' . __CLASS__ . ' object.');
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException('Attempt to mutate immutable ' . __CLASS__ . ' object.');
    }

    /**
     * JsonSerializable
     */
    public function jsonSerialize(): array
    {
        return $this->sfa->toArray();
    }
}
