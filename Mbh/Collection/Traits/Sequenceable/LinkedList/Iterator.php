<?php namespace Mbh\Collection\Traits\Sequenceable\LinkedList;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\Internal\LinkedDataNode;
use Traversable;

trait Iterator
{
    protected $head;
    protected $current;
    protected $offset = -1;

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

    abstract protected function backward();

    abstract protected function forward();
}
