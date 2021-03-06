<?php namespace Mbh\Collection\Interfaces;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Countable;
use JsonSerializable;
use Traversable;
use Serializable;

/**
 * Collection is the base interface which covers functionality common to all the
 * data structures in this library. It guarantees that all structures are
 * traversable, countable, and can be converted to json using json_encode().
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */

interface Collection extends Countable, JsonSerializable, Serializable
{
    /**
     * Removes all values from the collection.
     */
    public function clear();

    /**
     * Returns the size of the collection.
     *
     * @return int
     */
    public function count(): int;

    /**
     * Returns a shallow copy of the collection.
     *
     * @return Collection a copy of the collection.
     */
    public function copy();

    /**
     * Returns whether the collection is empty.
     *
     * This should be equivalent to a count of zero, but is not required.
     * Implementations should define what empty means in their own context.
     *
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Returns an array representation of the collection.
     *
     * The format of the returned array is implementation-dependent.
     * Some implementations may throw an exception if an array representation
     * could not be created.
     *
     * @return array
     */
    public function toArray(): array;
}
