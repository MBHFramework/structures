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
final class DoublyLinkedList implements AllocatedInterface, SequenceableInterface
{
    use Traits\Collection;
    use Traits\Functional;
    use Traits\Builder;
    use Capacity;
    use EmptyGuard;

    const MIN_CAPACITY = 8.0;

    private $head;
    private $tail;
    private $size = 0;
    private $current;
    private $offset = -1;

    /**
     * Create an fixed array
     *
     * @param array|Traversable $array data
     */
    public function __construct($array = null)
    {
        $this->init();

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

    private function backward()
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

    /**
     * @link http://php.net/manual/en/countable.count.php
     * @return int
     */
    public function count(): int
    {
        return $this->size;
    }

    private function forward()
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

    protected function getValues(): Traversable
    {
        return SplFixedArray::fromArray($this->toArray());
    }

    private function guardedSeek($index, $method)
    {
        $index = $this->intGuard($index);
        $this->indexGuard($index, $method);

        return $this->seekTo($index);
    }

    private function init()
    {
        $this->head = $head = new LinkedTerminalNode();
        $this->tail = $tail = new LinkedTerminalNode();
    }

    private function indexGuard($offset, $method)
    {
        if (!$this->offsetExists($offset)) {
            throw new OutOfBoundsException(
                "{$method} was called with invalid index: {$offset}"
            );
        }
    }

    /**
     * @param $value
     * @param callable $callback
     * @return int
     */
    public function indexOf($value, callable $callback = null)
    {
        $equal = $f ?? function ($a, $b) {
            return $a === $b;
        };

        $filter = $this->filter(function ($item) use ($equal, $value) {
            return $equal($item, $value);
        });

        foreach ($filter as $key => $value) {
            return $key;
        }

        return -1;
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

    public function isEmpty(): bool
    {
        return $this->size === 0;
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

    private function pushAll($values)
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

    private function removeNode(LinkedNode $n)
    {
        $prev = $n->prev();
        $next = $n->next();

        $prev->setNext($next);
        $next->setPrev($prev);

        $this->size--;
    }

    public function search($value)
    {
        return $this->indexOf($value);
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
     * Iterator
     */

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
     * ArrayAccess
     */

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
}
