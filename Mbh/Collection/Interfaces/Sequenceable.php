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
use Iterator;
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

interface Sequenceable extends ArrayAccess, Collection, Iterator
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
     * Determines whether the sequence contains all of zero or more values.
     *
     * @param mixed ...$values
     *
     * @return bool true if at least one value was provided and the sequence
     *              contains all given values, false otherwise.
     */
    public function contains(...$values): bool;

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
     * Adds zero or more values to the front of the sequence.
     *
     * @param mixed ...$values
     */
    public function unshift(...$values);
}
