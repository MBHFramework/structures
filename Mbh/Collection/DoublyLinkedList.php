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
use Mbh\Collection\Internal\Interfaces\LinkedNode;
use Mbh\Collection\Internal\LinkedDataNode;
use Mbh\Collection\Internal\LinkedTerminalNode;
use Mbh\Interfaces\Allocated as AllocatedInterface;
use Mbh\Traits\Capacity;
use Mbh\Traits\EmptyGuard;
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
    use Traits\Collection;
    use Traits\Functional;
    use Capacity;
    use EmptyGuard;

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

    public function unshift($value)
    {
        $this->insertBetween($this->head, $this->head->next(), $value);
        $this->offset = 0;
    }

    public function pop()
    {
        $this->emptyGuard(__METHOD__);

        $n = $this->seekTail();
        $this->removeNode($n);

        return $n->value();
    }

    public function shift()
    {
        $this->emptyGuard(__METHOD__);

        $n = $this->seekHead();
        $this->removeNode($n);

        return $n->value();
    }

    public function first()
    {
        $this->emptyGuard(__METHOD__);
        return $this->seekHead()->value();
    }

    public function last()
    {
        $this->emptyGuard(__METHOD__);
        return $this->seekTail()->value();
    }

    /**
     * @link http://php.net/manual/en/countable.count.php
     * @return int
     */
    public function count()
    {
        return $this->size;
    }

    /**
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param int $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        $index = $this->intGuard($offset);
        return $index >= 0 && $index < $this->count();
    }

    /**
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param int $offset
     * @return mixed
     * @throws OutOfBoundsException
     */
    public function offsetGet($offset)
    {
        $n = $this->guardedSeek($offset, __METHOD__);
        return $n->value();
    }

    /**
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param int|null $offset
     * @param mixed $value
     * @return void
     * @throws OutOfBoundsException
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->push($value);
            return;
        }
        $n = $this->guardedSeek($offset, __METHOD__);
        $n->setValue($value);
    }

    /**
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param int $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $index = $this->intGuard($offset);
        if ($this->offsetExists($index)) {
            $n = $this->seekTo($index);
            $this->removeNode($n);
            $this->current = $n->prev();
            $this->offset--;
        }
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
    public function insertBefore(int $position, $value)
    {
        $n = $this->guardedSeek($position, __METHOD__);
        $this->insertBetween($n->prev(), $n, $value);
        $this->current = $this->current->next();
        $this->offset++;
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
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed
     */
    public function current()
    {
        return $this->current->value();
    }

    /**
     * @link http://php.net/manual/en/iterator.next.php
     * @return void
     */
    public function next()
    {
        $this->forward();
    }

    /**
     * @return void
     */
    public function prev()
    {
        $this->backward();
    }

    /**
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed
     */
    public function key()
    {
        return $this->offset;
    }

    /**
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean
     */
    public function valid()
    {
        return $this->current instanceof LinkedDataNode;
    }

    /**
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void
     */
    public function rewind()
    {
        $this->current = $this->head;
        $this->offset = -1;
        $this->forward();
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
     * @param mixed $i
     * @return int
     * @throws Exception
     */
    private function intGuard($i)
    {
        if (filter_var($i, FILTER_VALIDATE_INT) === false) {
            throw new Exception;
        }

        return (int) $i;
    }


    /**
     * @param $value
     * @param callable $f
     * @return int
     */
    public function indexOf($value, callable $f = null)
    {
        $equal = $f;

        $filter = $this->filter(function($item) use ($equal, $value) {
            return $equal($item, $value);
        });

        foreach ($filter as $key => $value) {
            return $key;
        }

        return -1;
    }

    /**
     * @param $value
     * @param callable $f [optional]
     * @return bool
     */
    public function contains($value, callable $f = null)
    {
        return $this->indexOf($value, $f) >= 0;
    }

    /**
     * Extract the elements after the first of a list, which must be non-empty.
     * @return LinkedList
     * @throws EmptyException
     */
    public function tail()
    {
        $this->emptyGuard(__METHOD__);
        return $this->copyFromContext($this->head->next()->next());
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

    private function copyFromContext(LinkedNode $context)
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

    private function insertBetween(LinkedNode $a, LinkedNode $b, $value)
    {
        $n = new LinkedDataNode($value);

        $a->setNext($n);
        $b->setPrev($n);

        $n->setPrev($a);
        $n->setNext($b);

        $this->current = $n;
        $this->size++;
    }

    private function forward()
    {
        $this->current = $this->current->next();
        $this->offset++;
    }

    private function backward()
    {
        $this->current = $this->current->prev();
        $this->offset--;
    }

    private function removeNode(LinkedNode $n)
    {
        $prev = $n->prev();
        $next = $n->next();

        $prev->setNext($next);
        $next->setPrev($prev);

        $this->size--;
    }

    /**
     * @return LinkedDataNode
     */
    private function seekTail()
    {
        $this->offset = $this->size - 1;
        return $this->current = $this->tail->prev();
    }

    /**
     * @return LinkedDataNode
     */
    private function seekHead()
    {
        $this->offset = 0;
        return $this->current = $this->head->next();
    }

    /**
     * @param $offset
     * @return LinkedDataNode
     */
    private function seekTo($offset)
    {
        $n = abs($diff = $this->offset - $offset);
        $action = ($diff < 0) ? 'forward' : 'backward';

        for ($i = 0; $i < $n; $i++) {
            $this->$action();
        }

        return $this->current;
    }

    private function indexGuard($offset, $method)
    {
        if (!$this->offsetExists($offset)) {
            throw new OutOfBoundsException(
                "{$method} was called with invalid index: {$offset}"
            );
        }
    }

    private function guardedSeek($index, $method)
    {
        $index = $this->intGuard($index);
        $this->indexGuard($index, $method);

        return $this->seekTo($index);
    }
}
