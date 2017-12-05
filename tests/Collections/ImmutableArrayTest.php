<?php

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use PHPUnit\Framework\TestCase;
use Mbh\Collection\ImmutableArray;
use Mbh\Collection\CallbackHeap;

/**
 * Test cases for verifying each ImmutableArray method
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */

// A heap for testing sorting
class BasicHeap extends SplHeap
{
    public function compare($a, $b)
    {
        return strcmp($a, $b);
    }
}

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

class ImmutableArrayTest extends TestCase
{
    public function testMap()
    {
        $base = [1, 2, 3, 4];
        $doubled = [2, 4, 6, 8];

        $numberSet = ImmutableArray::fromArray($base);
        $mapped = $numberSet->map(function ($num) {
            return $num * 2;
        });

        foreach ($mapped as $i => $v) {
            $this->assertEquals($v, $doubled[$i]);
        }
    }

    public function testFilter()
    {
        $oddArr = [1, 3, 5, 7, 9];
        $immArr = ImmutableArray::fromArray([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $odds = $immArr->filter(function ($num) {
            return $num % 2;
        });

        foreach ($odds as $i => $v) {
            $this->assertEquals($v, $oddArr[$i]);
        }
    }

    public function testReduce()
    {
        $arIt = new ArrayIterator([1, 2, 3, 4, 5]);
        $numberSet = ImmutableArray::fromItems($arIt);

        // Reduce with sum
        $sum = $numberSet->reduce(function ($last, $cur) {
            return $last + $cur;
        }, 0);

        $this->assertEquals(15, $sum);

        // Reduce with string concat
        $concatted = $numberSet->reduce(function ($last, $cur, $i) {
            return $last . '{"'. $i . '":"' . $cur . '"},';
        }, '');

        $this->assertEquals('{"0":"1"},{"1":"2"},{"2":"3"},{"3":"4"},{"4":"5"},', $concatted);
    }

    public function testJoin()
    {
        $imarr = ImmutableArray::fromArray(['foo', 'bar', 'baz']);

        $this->assertEquals('foo,bar,baz', $imarr->join(), 'Default join failed.');
        $this->assertEquals('fooXXXbarXXXbaz', $imarr->join('XXX'), 'Token join failed.');
        $this->assertEquals('<li>foo</li><li>bar</li><li>baz</li>', $imarr->join('<li>', '</li>'), 'Two token join failed.');
    }

    public function testSlice()
    {
        $immArr = ImmutableArray::fromArray([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $firstThree = $immArr->slice(0, 3);

        $this->assertCount(3, $firstThree);
        $this->assertSame([1, 2, 3], $firstThree->toArray());
    }

    public function testConcat()
    {
        $setA = ImmutableArray::fromArray([1, 2, 3]);
        $setB = ImmutableArray::fromItems(new ArrayIterator([4, 5, 6]));

        $concatted = $setA->concat($setB);
        $this->assertSame([1, 2, 3, 4, 5, 6], $concatted->toArray());
    }

    public function testSorted()
    {
        $unsorted = ImmutableArray::fromArray(['f', 'c', 'a', 'b', 'e', 'd']);
        $sorted = $unsorted->sorted(function ($a, $b) {
            return strcmp($a, $b);
        });

        $this->assertSame($sorted->toArray(), ['a', 'b', 'c', 'd', 'e', 'f'], 'Callback sort failed.');
    }

    public function testHeapSort()
    {
        $unsorted = ImmutableArray::fromArray(['f', 'c', 'a', 'b', 'e', 'd']);

        $heapSorted = $unsorted->heapSort(new BasicHeap());
        $this->assertSame($heapSorted->toArray(), ['a', 'b', 'c', 'd', 'e', 'f'], 'Heap sort failed.');
    }

    public function testLoadBigSet()
    {
        $startMemoryLimit = ini_get('memory_limit');
        ini_set('memory_limit', '50M');

        // Big Set Load
        $bigSet = ImmutableArray::fromItems(new BigSetIterator(200000));
        $this->assertCount(200000, $bigSet);
        ini_set('memory_limit', $startMemoryLimit);
    }
}
