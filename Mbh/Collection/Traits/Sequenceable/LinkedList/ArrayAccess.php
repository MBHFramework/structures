<?php namespace Mbh\Collection\Traits\Sequenceable\LinkedList;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\Internal\Interfaces\LinkedNode;
use Mbh\Collection\Internal\LinkedDataNode;
use Traversable;
use OutOfBoundsException;
use OutOfRangeException;

trait ArrayAccess
{
    protected $current;
    protected $offset = -1;

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

    /**
     * @link http://php.net/manual/en/countable.count.php
     * @return int
     */
    abstract public function count(): int;

    /**
     * @param mixed $i
     * @return int
     * @throws Exception
     */
    abstract protected function intGuard($i);

    abstract protected function guardedSeek($index, $method);

    abstract public function push(...$values);

    abstract protected function removeNode(LinkedNode $n);

    /**
     * @param $offset
     * @return LinkedDataNode
     */
    abstract protected function seekTo($offset);
}
