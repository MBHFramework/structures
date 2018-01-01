<?php namespace Mbh\Collection\Traits\Sequenceable;

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
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

trait LinkedList
{
    use LinkedList\ArrayAccess;
    use LinkedList\Countable;
    use LinkedList\Iterator;

    protected $head;
    protected $tail;
    protected $size;
    protected $current;
    protected $offset = -1;

    /**
     * Create an fixed array
     *
     * @param array|Traversable $array data
     */
    public function __construct($array = null)
    {
        $this->head = $head = new LinkedTerminalNode();
        $this->tail = $tail = new LinkedTerminalNode();

        $head->setNext($tail);
        $tail->setPrev($head);

        $this->current = $this->head;

        if ($array) {
            $this->pushAll($array);
        }
    }

    public function __clone()
    {
        $list = $this->copyFromContext($this->head->next());

        $this->head = $list->head;
        $this->tail = $list->tail;

        $this->current = $this->head;
        $this->offset = -1;

        $this->size = $list->size;
    }

    protected function backward()
    {
        $this->current = $this->current->prev();
        $this->offset--;
    }

    public function clear()
    {
        $this->head->setNext($this->tail);
        $this->tail->setPrev($this->head);

        $this->current = $this->head;
        $this->size = 0;
        $this->offset = -1;
    }

    public function contains(...$values): bool
    {
        return $this->indexOf($values, $f) >= 0;
    }

    public function copy()
    {
        return $this->copyFromContext($this->head->next());
    }

    protected function copyFromContext(LinkedNode $context)
    {
        $list = new static();

        for ($n = $context; $n !== $this->tail; $n = $n->next()) {
            /**
             * @var LinkedDataNode $n
             */
            $list->push($n->value());
        }

        return $list;
    }

    protected function forward()
    {
        $this->current = $this->current->next();
        $this->offset++;
    }

    public function first()
    {
        $this->emptyGuard(__METHOD__);
        return $this->seekHead()->value();
    }

    public function get(int $index)
    {
        return $this[$index];
    }

    protected function getSize()
    {
        return $this->size;
    }

    protected function getValues(): Traversable
    {
        return SplFixedArray::fromArray($this->toArray());
    }

    protected function guardedSeek($index, $method)
    {
        $index = $this->intGuard($index);
        $this->indexGuard($index, $method);

        return $this->seekTo($index);
    }

    protected function indexGuard($offset, $method)
    {
        if (!$this->offsetExists($offset)) {
            throw new OutOfBoundsException(
                "{$method} was called with invalid index: {$offset}"
            );
        }
    }

    /**
     * @param $value
     * @return int
     */
    public function indexOf($value)
    {
        if (($index = $this->search($value)) === null) {
            return -1;
        }

        return $index;
    }

    public function insert(int $index, ...$values)
    {
        foreach ($values as &$value) {
            $this->insertBefore($index++, $value);
        }
    }

    /**
     * @param int $position
     * @param mixed $value
     * @return void
     *
     * @throws OutOfBoundsException
     */
    public function insertAfter(int $position, $value)
    {
        $n = $this->guardedSeek($position, __METHOD__);
        $this->insertBetween($n, $n->next(), $value);
        $this->current = $this->current->prev();
    }

    /**
     * @param int $position
     * @param mixed $value
     * @return void
     *
     * @throws OutOfBoundsException
     */
    public function insertBefore(int $position, $value)
    {
        $n = $this->guardedSeek($position, __METHOD__);
        $this->insertBetween($n->prev(), $n, $value);
        $this->current = $this->current->next();
        $this->offset++;
    }

    protected function insertBetween(LinkedNode $a, LinkedNode $b, $value)
    {
        $n = new LinkedDataNode($value);

        $a->setNext($n);
        $b->setPrev($n);

        $n->setPrev($a);
        $n->setNext($b);

        $this->current = $n;
        $this->size++;
    }

    /**
     * @param mixed $i
     * @return int
     * @throws Exception
     */
    protected function intGuard($i)
    {
        if (filter_var($i, FILTER_VALIDATE_INT) === false) {
            throw new Exception;
        }

        return (int) $i;
    }

    public function last()
    {
        $this->emptyGuard(__METHOD__);
        return $this->seekTail()->value();
    }

    public function pop()
    {
        $this->emptyGuard(__METHOD__);

        $n = $this->seekTail();
        $this->removeNode($n);

        return $n->value();
    }

    public function push(...$values)
    {
        $this->pushAll($values);
    }

    protected function pushAll($values)
    {
        foreach ($values as $key => $value) {
            $this->insertBetween($this->tail->prev(), $this->tail, $value);
            $this->offset = $this->size - 1;
        }
    }

    public function remove(int $index)
    {
        return $this->removeNode($this->guardedSeek($index, __METHOD__));
    }

    protected function removeNode(LinkedNode $n)
    {
        $prev = $n->prev();
        $next = $n->next();

        $prev->setNext($next);
        $next->setPrev($prev);

        $this->size--;
    }

    /**
     * @link http://php.net/manual/en/seekableiterator.seek.php
     * @param int $position
     * @return mixed
     * @throws OutOfBoundsException
     * @throws Exception
     */
    public function seek($position)
    {
        $index = $this->intGuard($position);
        $this->indexGuard($index, __METHOD__);

        if ($index === 0) {
            return $this->seekHead()->value();
        } elseif ($index === $this->size - 1) {
            return $this->seekTail()->value();
        }

        return $this->seekTo($index)->value();
    }

    /**
     * @return LinkedDataNode
     */
    protected function seekTail()
    {
        $this->offset = $this->size - 1;
        return $this->current = $this->tail->prev();
    }

    /**
     * @return LinkedDataNode
     */
    protected function seekHead()
    {
        $this->offset = 0;
        return $this->current = $this->head->next();
    }

    /**
     * @param $offset
     * @return LinkedDataNode
     */
    protected function seekTo($offset)
    {
        $n = abs($diff = $this->offset - $offset);
        $action = ($diff < 0) ? 'forward' : 'backward';

        for ($i = 0; $i < $n; $i++) {
            $this->$action();
        }

        return $this->current;
    }

    /**
     * @inheritDoc
     */
    public function set(int $index, $value)
    {
        $this->offsetSet($index, $value);
    }

    protected function setValues(Traversable $traversable)
    {
        $this->clear();
        $this->pushAll($traversable);
    }

    public function shift()
    {
        $this->emptyGuard(__METHOD__);

        $n = $this->seekHead();
        $this->removeNode($n);

        return $n->value();
    }

    /**
     * Extract the elements after the first of a list, which must be non-empty.
     * @return DoublyLinkedList
     * @throws EmptyException
     */
    public function tail()
    {
        $this->emptyGuard(__METHOD__);
        return $this->copyFromContext($this->head->next()->next());
    }

    public function toArray(): array
    {
        $array = [];
        $context = $this->head->next();

        for ($n = $context; $n !== $this->tail; $n = $n->next()) {
            /**
             * @var LinkedDataNode $n
             */
            $array[] = $n->value();
        }

        return $array;
    }

    public function unserialize($serialized)
    {
    }

    public function unshift(...$values)
    {
        foreach ($values as &$value) {
            $this->insertBetween($this->head, $this->head->next(), $value);
            $this->offset = 0;
        }
    }

    /**
     * @inheritDoc
     */
    protected function validIndex(int $index)
    {
        return $this->offsetExists($index);
    }

    abstract public function isEmpty(): bool;

    abstract public function search($value);
}
