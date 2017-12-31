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
use Mbh\Collection\Internal\LinkedDataNode;
use Mbh\Collection\Internal\LinkedTerminalNode;
use Mbh\Interfaces\Allocated as AllocatedInterface;
use Mbh\Traits\Capacity;
use Mbh\Collection\Interfaces\Functional as FunctionalInterface;
use Mbh\Collection\Interfaces\Sequenceable as SequenceableInterface;
use Mbh\Interfaces\Allocated as AllocatedInterface;
use Mbh\Traits\Capacity;

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
    use Traits\Collection;
    use Traits\Functional;
    use Capacity;

    const MIN_CAPACITY = 8.0;

    private $head;
    private $tail;
    private $size = 0;
    private $current;
    private $offset = -1;

    public function __construct()
    {
        $this->head = $head = new LinkedTerminalNode();
        $this->tail = $tail = new LinkedTerminalNode();

        $head->setNext($tail);
        $tail->setPrev($head);

        $this->current = $this->head;
    }

    public function isEmpty()
    {
        return $this->size === 0;
    }

    public function push(...$values)
    {
        foreach ($values as $key => $value) {
            $this->insertBetween($this->tail->prev(), $this->tail, $value);
            $this->offset = $this->size - 1;
        }
    }
}
