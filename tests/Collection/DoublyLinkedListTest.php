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
}
