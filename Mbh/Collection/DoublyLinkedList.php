<?php namespace Mbh\Collection;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\Interfaces\Collection as CollectionInterface;
use Mbh\Collection\Interfaces\Functional as FunctionalInterface;
use Mbh\Collection\Interfaces\Sequenceable as SequenceableInterface;
use Mbh\Collection\Internal\Interfaces\LinkedNode;
use Mbh\Collection\Internal\LinkedDataNode;
use Mbh\Collection\Internal\LinkedTerminalNode;
use Mbh\Interfaces\Allocated as AllocatedInterface;
use Mbh\Traits\Capacity;
use Mbh\Traits\EmptyGuard;
use SplFixedArray;
use Traversable;
use OutOfBoundsException;
use Exception;

/**
 * The DoublyLinkedList
 *
 *
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */
final class DoublyLinkedList implements AllocatedInterface, FunctionalInterface, SequenceableInterface
{
    use Capacity;
    use EmptyGuard;
    use Traits\Builder;
    use Traits\Collection;
    use Traits\Functional;
    use Traits\Sequenceable;
    use Traits\Sequenceable\LinkedList;

    const MIN_CAPACITY = 8.0;

    protected $head;
    protected $tail;
    protected $size = 0;
    protected $current;
    protected $offset = -1;
}
