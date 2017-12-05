<?php namespace Mbh\Tests\Collection;

use Iterator;
use Countable;

// A basic iterator for testing loading large sets
class BigSetIterator implements Iterator, Countable
{
    protected $count;
    protected $position = 0;

    public function __construct($count = 0)
    {
        $this->count = $count;
    }
    public function rewind()
    {
        $this->position = 0;
    }
    public function current()
    {
        return md5($this->position);
    }
    public function key()
    {
        return $this->position;
    }
    public function next()
    {
        ++$this->position;
    }
    public function valid()
    {
        return $this->position < $this->count;
    }
    public function count()
    {
        return $this->count;
    }
}
