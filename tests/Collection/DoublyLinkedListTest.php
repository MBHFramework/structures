<?php namespace Mbh\Tests\Collection;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use PHPUnit\Framework\TestCase;
use Mbh\Collection\DoublyLinkedList;
use ArrayIterator;

/**
 * Test cases for verifying each DoublyLinkedList method
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */

class DoublyLinkedListTest extends TestCase
{
    public function testPush()
    {
        $other = [1, 2, 3];

        $list = new DoublyLinkedList;

        $list->push(1);
        $list->push(2);
        $list->push(3);

        $this->assertEquals($other, $list->toArray());
    }

    public function testPop()
    {
        $other = [1, 2];

        $list = new DoublyLinkedList;

        $list->push(1);
        $list->push(2);
        $list->push(3);
        $list->pop();

        $this->assertEquals($other, $list->toArray());
    }

    public function testRemove()
    {
        $other = [1, 3];

        $list = new DoublyLinkedList;

        $list->push(1);
        $list->push(2);
        $list->push(3);
        $list->remove(1);

        $this->assertEquals($other, $list->toArray());
    }

    public function testInsert()
    {
        $base = [1, 2, 3, 4, 7];
        $other = [1, 2, 3, 4, 5, 6, 7];

        $numberSet = DoublyLinkedList::fromArray($base);

        $numberSet->insert(4, 5, 6);

        $this->assertEquals($other, $numberSet->toArray());
    }

    public function testMap()
    {
        $base = [1, 2, 3, 4];
        $doubled = [2, 4, 6, 8];

        $numberSet = DoublyLinkedList::fromArray($base);
        $mapped = $numberSet->map(function ($num) {
            return $num * 2;
        });

        foreach ($mapped as $i => $v) {
            $this->assertEquals($v, $doubled[$i]);
        }
    }

    public function testContains()
    {
        $base = [1, 2, 3, 4, 5, 6, 7];
        $other = [1, 2, 3, 4, 7];

        $numberSet = DoublyLinkedList::fromArray($base);

        $contains = $numberSet->contains($other);

        $this->assertEquals(true, $contains);
    }

    public function testFilter()
    {
        $oddArr = [1, 3, 5, 7, 9];
        $list = DoublyLinkedList::fromArray([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $odds = $list->filter(function ($num) {
            return $num % 2;
        });

        foreach ($odds as $i => $v) {
            $this->assertEquals($v, $oddArr[$i]);
        }
    }

    public function testReduce()
    {
        $arIt = new ArrayIterator([1, 2, 3, 4, 5]);
        $numberSet = DoublyLinkedList::fromItems($arIt);

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
        $list = DoublyLinkedList::fromArray(['foo', 'bar', 'baz']);

        $this->assertEquals('foo,bar,baz', $list->join(), 'Default join failed.');
        $this->assertEquals('fooXXXbarXXXbaz', $list->join('XXX'), 'Token join failed.');
        $this->assertEquals('<li>foo</li><li>bar</li><li>baz</li>', $list->join('<li>', '</li>'), 'Two token join failed.');
    }

    public function testSlice()
    {
        $list = DoublyLinkedList::fromArray([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $firstThree = $list->slice(0, 3);

        $this->assertCount(3, $firstThree);
        $this->assertSame([1, 2, 3], $firstThree->toArray());
    }

    public function testConcat()
    {
        $setA = DoublyLinkedList::fromArray([1, 2, 3]);
        $setB = DoublyLinkedList::fromItems(new ArrayIterator([4, 5, 6]));

        $concatted = $setA->concat($setB);
        $this->assertSame([1, 2, 3, 4, 5, 6], $concatted->toArray());
    }

    public function testSorted()
    {
        $unsorted = DoublyLinkedList::fromArray(['f', 'c', 'a', 'b', 'e', 'd']);
        $sorted = $unsorted->sorted(function ($a, $b) {
            return strcmp($a, $b);
        });

        $this->assertSame($sorted->toArray(), ['a', 'b', 'c', 'd', 'e', 'f'], 'Callback sort failed.');
    }

    public function testHeapSorted()
    {
        $unsorted = DoublyLinkedList::fromArray(['f', 'c', 'a', 'b', 'e', 'd']);

        $heapSorted = $unsorted->heapSorted(new BasicHeap());
        $this->assertSame($heapSorted->toArray(), ['a', 'b', 'c', 'd', 'e', 'f'], 'Heap sort failed.');
    }
}
