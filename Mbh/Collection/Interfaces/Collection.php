<?php namespace Mbh\Collection\Interfaces;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Iterator;
use Countable;
use JsonSerializable;
use Traversable;

/**
  * Collection is the base interface which covers functionality common to all the
  * data structures in this library. It guarantees that all structures are
  * traversable, countable, and can be converted to json using json_encode().
  *
  * @package structures
  * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
  */

interface Collection extends Iterator, Countable, JsonSerializable
{
}
