<?php namespace Mbh\Collection;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\Interfaces\Collection as CollectionInterface;
use ArrayAccess;
use IteratorAggregate;
use OutOfBoundsException;
use OutOfRangeException;

/**
 * A sequence of unique values.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */
class Set implements ArrayAccess, CollectionInterface, IteratorAggregate
{
    use Traits\Collection;
}
