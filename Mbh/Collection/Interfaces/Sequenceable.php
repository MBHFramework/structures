<?php namespace Mbh\Collection\Interfaces;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Traversable;
use ArrayAccess;
use SplHeap;
use UnderflowException;
use OutOfRangeException;

/**
 * Sequenceable Collection is the base interface which covers functionality common to
 * most of the data structures in this library. It guarantees that all structures are
 * array accessables.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */

interface Sequenceable extends Collection, ArrayAccess
{
    /**
     * Build from an array
     *
     * @return Sequenceable
     */
    public static function fromArray(array $array);

    /**
     * Factory for building FixedArrays from any traversable
     *
     * @return Sequenceable
     */
    public static function fromItems(Traversable $array);

    /**
     * Ensures that enough memory is allocated for a specified capacity. This
     * potentially reduces the number of reallocations as the size increases.
     *
     * @param int $capacity The number of values for which capacity should be
     *                      allocated. Capacity will stay the same if this value
     *                      is less than or equal to the current capacity.
     */
    public function allocate(int $capacity);

    /**
     * Returns the current capacity.
     *
     * @return int
     */
    public function capacity(): int;

    /**
     * Concat to the end of this array
     *
     * @param Traversable,...
     * @return Sequenceable
     */
    public function concat();

    /**
     * Determines whether the sequence contains all of zero or more values.
     *
     * @param mixed ...$values
     *
     * @return bool true if at least one value was provided and the sequence
     *              contains all given values, false otherwise.
     */
    public function contains(...$values): bool;

    /**
     * Creates a shallow copy of the collection.
     *
     * @return Collection a shallow copy of the collection.
     */
    public function copy();

    /**
     * Join a set of strings together.
     *
     * @param string $token Main token to put between elements
     * @param string $secondToken If set, $token on left $secondToken on right
     * @return string
     */
    public function join(string $token = ',', string $secondToken = null): string;

    /**
     * Filter out elements
     *
     * @param callable $callback Function to filter out on false
     * @return Sequenceable
     */
    public function filter(callable $callback);

    /**
     * Find a single element
     *
     * @param callable $callback The test to run on each element
     * @return mixed The element we found
     */
    public function find(callable $callback);

    /**
     * Returns the first value in the sequence.
     *
     * @return mixed
     *
     * @throws UnderflowException if the sequence is empty.
     */
    public function first();

    /**
     * Returns the value at a given index (position) in the sequence.
     *
     * @param int $index
     *
     * @return mixed
     *
     * @throws OutOfRangeException if the index is not in the range [0, size-1]
     */
    public function get(int $index);

    /**
     * Sort a new Sequenceable by filtering through a heap.
     * Tends to run much faster than array or merge sorts, since you're only
     * sorting the pointers, and the sort function is running in a highly
     * optimized space.
     *
     * @param SplHeap $heap The heap to run for sorting
     * @return Sequenceable
     */
    public function heapSort(SplHeap $heap);

    /**
     * Inserts zero or more values at a given index.
     *
     * Each value after the index will be moved one position to the right.
     * Values may be inserted at an index equal to the size of the sequence.
     *
     * @param int   $index
     * @param mixed ...$values
     *
     * @throws OutOfRangeException if the index is not in the range [0, n]
     */
    public function insert(int $index, ...$values);

    /**
     * Returns the last value in the sequence.
     *
     * @return mixed
     *
     * @throws UnderflowException if the sequence is empty.
     */
    public function last();

    /**
     * Map elements to a new Sequenceable via a callback
     *
     * @param callable $callback Function to map new data
     * @return Sequenceable
     */
    public function map(callable $callback);

    /**
     * Removes the last value in the sequence, and returns it.
     *
     * @return mixed what was the last value in the sequence.
     *
     * @throws UnderflowException if the sequence is empty.
     */
    public function pop();

    /**
     * Adds zero or more values to the end of the sequence.
     *
     * @param mixed ...$values
     */
    public function push(...$values);

    /**
     * Removes and returns the value at a given index in the sequence.
     *
     * @param int $index this index to remove.
     *
     * @return mixed the removed value.
     *
     * @throws OutOfRangeException if the index is not in the range [0, size-1]
     */
    public function remove(int $index);

    /**
     * Reduce to a single value
     *
     * @param callable $callback Callback(
     *     mixed $previous, mixed $current[, mixed $index, mixed $immArray]
     * ):mixed Callback to run reducing function
     * @param mixed $accumulator Initial value for first argument
     */
    public function reduce(callable $callback, $accumulator = null);

    /**
     * Find a single element key
     *
     * @param mixed $value The value to search
     * @return mixed The key for the element we found
     */
    public function search($value);

    /**
     * Replaces the value at a given index in the sequence with a new value.
     *
     * @param int   $index
     * @param mixed $value
     *
     * @throws OutOfRangeException if the index is not in the range [0, size-1]
     */
    public function set(int $index, $value);

    /**
     * Removes and returns the first value in the sequence.
     *
     * @return mixed what was the first value in the sequence.
     *
     * @throws UnderflowException if the sequence was empty.
     */
    public function shift();

    /**
     * Take a slice of the array
     *
     * @param int $begin Start index of slice
     * @param int $end End index of slice
     * @return Sequenceable
     */
    public function slice(int $begin = 0, int $end = null);

    /**
     * Return a new sorted Sequenceable
     *
     * @param callable $callback The sort callback
     * @return Sequenceable
     */
    public function sort(callable $callback = null);

    /**
     * Adds zero or more values to the front of the sequence.
     *
     * @param mixed ...$values
     */
    public function unshift(...$values);

    /**
     * forEach, or "walk" the data
     * Exists primarily to provide a consistent interface, though it's seldom
     * any better than a simple php foreach. Mainly useful for chaining.
     * Named walk for historic reasons - forEach is reserved in PHP
     *
     * @param callable $callback Function to call on each element
     * @return Sequenceable
     */
    public function walk(callable $callback);
}
