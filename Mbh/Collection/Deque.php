<?php namespace Mbh\Collection;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\Interfaces\Functional as FunctionalInterface;
use Mbh\Collection\Interfaces\Sequenceable as SequenceableInterface;
use Mbh\Interfaces\Allocated as AllocatedInterface;
use Mbh\Traits\SquaredCapacity;
use Mbh\Traits\EmptyGuard;

/**
 * A Deque is a sequence of values in a contiguous buffer
 * that grows and shrinks automatically. The name is a common abbreviation of
 * "double-ended queue".
 *
 * While a Deque is very similar to a Vector, it offers constant time operations
 * at both ends of the buffer, ie. shift, unshift, push and pop are all O(1).
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */
class Deque implements AllocatedInterface, FunctionalInterface, SequenceableInterface
{
    use EmptyGuard;
    use SquaredCapacity;
    use Traits\Collection;
    use Traits\Functional;
    use Traits\Sequenceable;
    use Traits\Sequenceable\Arrayed;

    const MIN_CAPACITY = 8.0;
}
