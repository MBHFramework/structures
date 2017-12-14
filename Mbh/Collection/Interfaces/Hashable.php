<?php namespace Mbh\Collection\Interfaces;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

/**
 * Hashable is an interface which allows objects to be used as keys.
 *
 * It’s an alternative to spl_object_hash(), which determines an object’s hash
 * based on its handle: this means that two objects that are considered equal
 * by an implicit definition would not treated as equal because they are not
 * the same instance.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */
interface Hashable
{
    /**
     * Produces a scalar value to be used as the object's hash, which determines
     * where it goes in the hash table. While this value does not have to be
     * unique, objects which are equal must have the same hash value.
     *
     * @return mixed
     */
    public function hash();
    
    /**
     * Determines if two objects should be considered equal. Both objects will
     * be instances of the same class but may not be the same instance.
     *
     * @param $obj An instance of the same class to compare to.
     *
     * @return bool
     */
    public function equals($obj): bool;
}
