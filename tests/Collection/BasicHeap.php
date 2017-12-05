<?php namespace Mbh\Tests\Collection;

use SplHeap;

// A heap for testing sorting
class BasicHeap extends SplHeap
{
    public function compare($a, $b)
    {
        return strcmp($a, $b);
    }
}
