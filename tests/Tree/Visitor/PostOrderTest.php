<?php namespace Mbh\Tests\Tree\Visitor;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use PHPUnit\Framework\TestCase;
use Mbh\Tree\Visitor\PostOrder as PostOrderVisitor;
use Mbh\Tree\Node;

/**
 * Test cases for verifying each Visitor method
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */
class PostOrderTest extends TestCase
{
    public function testImplementsInterface()
    {
        $visitor = new PostOrderVisitor();
        $this->assertInstanceOf('Mbh\Tree\Visitor', $visitor);
    }

    /**
     * root
     */
    public function testWalkTreeWithOneNode()
    {
        $root = new Node('root');
        $visitor = new PostOrderVisitor();
        $expected = [
            $root,
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
        $visitor = new PostOrderVisitor();
        $expected = [
            $a,
            $root,
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

        $visitor = new PostOrderVisitor();
        $expected = [
            $d,
            $e,
            $a,
            $f,
            $b,
            $c,
            $root,
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

        $visitor = new PostOrderVisitor();
        $expected = [
            $d,
            $e,
            $a,
        ];

        $this->assertSame($expected, $visitor->visit($a));
    }
}
