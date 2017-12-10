<?php namespace Mbh\Tests\Tree\Visitor;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use PHPUnit\Framework\TestCase;
use Mbh\Tree\Visitor\PreOrder as PreOrderVisitor;
use Mbh\Tree\Node;

/**
 * Test cases for verifying each Builder method
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */
class PreOrderTest extends TestCase
{
    public function testImplementsInterface()
    {
        $visitor = new PreOrderVisitor();
        $this->assertInstanceOf('Mbh\Tree\Visitor', $visitor);
    }

    /**
     * root
     */
    public function testWalkTreeWithOneNode()
    {
        $root = new Node('root');

        $visitor = new PreOrderVisitor();
        $expected = [
          $root
        ];

        $this->assertSame($expected, $visitor->visit($root));
    }

    /**
     * root
     *  |
     *  a
     */
    public function testWalkTreeWithTwoNodes()
    {
        $root = new Node('root');
        $a = new Node('a');
        $root->addChild($a);

        $visitor = new PreOrderVisitor();
        $expected = [
          $root,
          $a
        ];

        $this->assertSame($expected, $visitor->visit($root));
    }

    /**
     *    root
     *    /|\
     *   a b c
     *  /| |
     * d e f
     */
    public function testWalkTreeWithMoreNodes()
    {
        $root = new Node('root');
        $a = new Node('a');
        $b = new Node('b');
        $c = new Node('c');
        $d = new Node('d');
        $e = new Node('e');
        $f = new Node('f');

        $root->addChild($a);
        $root->addChild($b);
        $root->addChild($c);
        $a->addChild($d);
        $a->addChild($e);
        $b->addChild($f);

        $visitor = new PreOrderVisitor();
        $expected = [
          $root,
          $a,
          $d,
          $e,
          $b,
          $f,
          $c
        ];

        $this->assertSame($expected, $visitor->visit($root));
    }

    /**
     *    root
     *    /|\
     *   a b c
     *  /| |
     * d e f
     */
    public function testWalkSubTree()
    {
        $root = new Node('root');
        $a = new Node('a');
        $b = new Node('b');
        $c = new Node('c');
        $d = new Node('d');
        $e = new Node('e');
        $f = new Node('f');

        $root->addChild($a);
        $root->addChild($b);
        $root->addChild($c);
        $a->addChild($d);
        $a->addChild($e);
        $b->addChild($f);

        $visitor = new PreOrderVisitor();
        $expected = [
          $a,
          $d,
          $e
        ];

        $this->assertSame($expected, $visitor->visit($a));
    }
}
