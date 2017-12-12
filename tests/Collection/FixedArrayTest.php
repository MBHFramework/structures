<?php namespace Mbh\Tests\Collection;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use PHPUnit\Framework\TestCase;
use Mbh\Collection\FixedArray;
use Mbh\Collection\CallbackHeap;
use ArrayIterator;

/**
 * Test cases for verifying each FixedArray method
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */

class FixedArrayTest extends TestCase
{
    public function testPush()
    {
        $base = [1, 2, 3, 4];
        $other = [1, 2, 3, 4, 5, 6, 7];

        $numberSet = FixedArray::fromArray($base);

        $numberSet->push(5);
        $numberSet->push(6);
        $numberSet->push(7);

        foreach ($other as $i => $v) {
            $this->assertEquals($v, $numberSet[$i]);
        }
    }

    public function testPop()
    {
        $base = [1, 2, 3, 4, 5, 6, 7];
        $other = [1, 2, 3, 4];

        $numberSet = FixedArray::fromArray($base);

        $numberSet->pop();
        $numberSet->pop();
        $numberSet->pop();

        foreach ($other as $i => $v) {
            $this->assertEquals($v, $numberSet[$i]);
        }
    }

    public function testInsert()
    {
        $base = [1, 2, 3, 4, 7];
        $other = [1, 2, 3, 4, 5, 6, 7];

        $numberSet = FixedArray::fromArray($base);

        $numberSet->insert(4, 5, 6);

        foreach ($other as $i => $v) {
            $this->assertEquals($v, $numberSet[$i]);
        }
    }

    public function testMap()
    {
        $base = [1, 2, 3, 4];
        $doubled = [2, 4, 6, 8];

        $numberSet = FixedArray::fromArray($base);
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
        $immArr = FixedArray::fromArray([1, 2, 3, 4, 5, 6, 7, 8, 9]);

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
        $numberSet = FixedArray::fromItems($arIt);

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
        $imarr = FixedArray::fromArray(['foo', 'bar', 'baz']);

        $this->assertEquals('foo,bar,baz', $imarr->join(), 'Default join failed.');
        $this->assertEquals('fooXXXbarXXXbaz', $imarr->join('XXX'), 'Token join failed.');
        $this->assertEquals('<li>foo</li><li>bar</li><li>baz</li>', $imarr->join('<li>', '</li>'), 'Two token join failed.');
    }

    public function testSlice()
    {
        $immArr = FixedArray::fromArray([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $firstThree = $immArr->slice(0, 3);

        $this->assertCount(3, $firstThree);
        $this->assertSame([1, 2, 3], $firstThree->toArray());
    }

    public function testConcat()
    {
        $setA = FixedArray::fromArray([1, 2, 3]);
        $setB = FixedArray::fromItems(new ArrayIterator([4, 5, 6]));

        $concatted = $setA->concat($setB);
        $this->assertSame([1, 2, 3, 4, 5, 6], $concatted->toArray());
    }

    public function testSorted()
    {
        $unsorted = FixedArray::fromArray(['f', 'c', 'a', 'b', 'e', 'd']);
        $sorted = $unsorted->sorted(function ($a, $b) {
            return strcmp($a, $b);
        });

        $this->assertSame($sorted->toArray(), ['a', 'b', 'c', 'd', 'e', 'f'], 'Callback sort failed.');
    }

    public function testHeapSorted()
    {
        $unsorted = FixedArray::fromArray(['f', 'c', 'a', 'b', 'e', 'd']);

        $heapSorted = $unsorted->heapSorted(new BasicHeap());
        $this->assertSame($heapSorted->toArray(), ['a', 'b', 'c', 'd', 'e', 'f'], 'Heap sort failed.');
    }

    public function testLoadBigSet()
    {
        $startMemoryLimit = ini_get('memory_limit');
        ini_set('memory_limit', '50M');

        // Big Set Load
        $bigSet = FixedArray::fromItems(new BigSetIterator(200000));
        $this->assertCount(200000, $bigSet);
        ini_set('memory_limit', $startMemoryLimit);
    }
}
