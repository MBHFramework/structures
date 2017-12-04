<?php namespace Mbh\Collection\Interfaces;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\Interfaces\Collection as CollectionInterface;
use ArrayAccess;
use SplHeap;

/**
 * Sequenceable Collection is the base interface which covers functionality common to
 * most of the data structures in this library. It guarantees that all structures are
 * array accessables.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */

interface Sequenceable extends CollectionInterface, ArrayAccess
{
    /**
     * Map elements to a new Sequenceable via a callback
     *
     * @param callable $callback Function to map new data
     * @return Sequenceable
     */
    public function map(callable $callback): Sequenceable;

    /**
     * forEach, or "walk" the data
     * Exists primarily to provide a consistent interface, though it's seldom
     * any better than a simple php foreach. Mainly useful for chaining.
     * Named walk for historic reasons - forEach is reserved in PHP
     *
     * @param callable $callback Function to call on each element
     * @return Sequenceable
     */
    public function walk(callable $callback): Sequenceable;

    /**
     * Filter out elements
     *
     * @param callable $callback Function to filter out on false
     * @return Sequenceable
     */
    public function filter(callable $callback): Sequenceable;

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
     * Join a set of strings together.
     *
     * @param string $token Main token to put between elements
     * @param string $secondToken If set, $token on left $secondToken on right
     * @return string
     */
    public function join(string $token = ',', string $secondToken = null): string;

    /**
     * Take a slice of the array
     *
     * @param int $begin Start index of slice
     * @param int $end End index of slice
     * @return Sequenceable
     */
    public function slice(int $begin = 0, int $end = null): Sequenceable;

    /**
     * Concat to the end of this array
     *
     * @param Traversable,...
     * @return Sequenceable
     */
    public function concat(): Sequenceable;

    /**
     * Find a single element
     *
     * @param callable $callback The test to run on each element
     * @return mixed The element we found
     */
    public function find(callable $callback);

    /**
     * Return a new sorted Sequenceable
     *
     * @param callable $callback The sort callback
     * @return Sequenceable
     */
    public function sort(callable $callback = null);

    /**
     * Sort a new Sequenceable by filtering through a heap.
     * Tends to run much faster than array or merge sorts, since you're only
     * sorting the pointers, and the sort function is running in a highly
     * optimized space.
     *
     * @param SplHeap $heap The heap to run for sorting
     * @return Sequenceable
     */
    public function heapSort(SplHeap $heap): Sequenceable;
}
