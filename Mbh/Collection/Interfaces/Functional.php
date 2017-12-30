<?php namespace Mbh\Collection\Interfaces;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use SplHeap;

/**
 * Common interface for collections that implements functional methods.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */

interface Functional
{
    /**
     *
     *
     * @param callable $callback Function to map new data
     * @return Collection
     */
    public function any(callable $callback);

    /**
     * Concat to the end of this array
     *
     * @param Traversable,...
     * @return Collection
     */
    public function concat();

    /**
     * Find a single element
     *
     * @param callable $callback The test to run on each element
     * @return mixed The element we found
     */
    public function find(callable $callback);

    /**
     * Filter out elements
     *
     * @param callable $callback Function to filter out on false
     * @return Collection
     */
    public function filter(callable $callback);

    /**
     * Sort the Collection by filtering through a heap.
     * Tends to run much faster than array or merge sorts, since you're only
     * sorting the pointers, and the sort function is running in a highly
     * optimized space.
     *
     * @param SplHeap $heap The heap to run for sorting
     * @return Collection
     */
    public function heapSort(SplHeap $heap);

    /**
     * Sort a new Collection by filtering through a heap.
     * Tends to run much faster than array or merge sorts, since you're only
     * sorting the pointers, and the sort function is running in a highly
     * optimized space.
     *
     * @param SplHeap $heap The heap to run for sorting
     * @return Collection
     */
    public function heapSorted(SplHeap $heap);

    /**
     * Join a set of strings together.
     *
     * @param string $token Main token to put between elements
     * @param string $secondToken If set, $token on left $secondToken on right
     * @return string
     */
    public function join(string $token = ',', string $secondToken = null): string;

    /**
     * Map elements to a new Collection via a callback
     *
     * @param callable $callback Function to map new data
     * @return Collection
     */
    public function map(callable $callback);

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
     * Take a slice of the array
     *
     * @param int $begin Start index of slice
     * @param int $end End index of slice
     * @return Collection
     */
    public function slice(int $begin = 0, int $length = null);

    /**
     * Remove a portion of the array and replace it with something else
     *
     * @param int $begin Start index of slice
     * @param int $end End index of slice
     * @param mixed $replacement
     * @return Collection
     */
    public function splice(int $begin = 0, int $length = null, $replacement = []);

    /**
     * Sort the Collection
     *
     * @param callable $callback The sort callback
     * @return Collection
     */
    public function sort(callable $callback = null);

    /**
     * Return a new sorted Collection
     *
     * @param callable $callback The sort callback
     * @return Collection
     */
    public function sorted(callable $callback = null);

    /**
     * forEach, or "walk" the data
     * Exists primarily to provide a consistent interface, though it's seldom
     * any better than a simple php foreach. Mainly useful for chaining.
     * Named walk for historic reasons - forEach is reserved in PHP
     *
     * @param callable $callback Function to call on each element
     * @return Collection
     */
    public function walk(callable $callback);
}
