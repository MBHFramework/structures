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

/**
 * Sequenceable Collection is the base interface which covers functionality common to
 * most of the data structures in this library. It guarantees that all structures are
 * array accessables.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */

interface SequenceableCollection extends CollectionInterface, ArrayAccess
{
}
