<?php namespace Mbh\Tests\Tree\Visitor;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use PHPUnit\Framework\TestCase;
use Mbh\Tree\Visitor\InOrder as InOrderVisitor;
use Mbh\Tree\Node;

/**
 * Test cases for verifying each Visitor method
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */
class InOrderTest extends TestCase
{
    /**
     *              root
     *              /  \
     *             A    B
     *            / \
     *           C   D
     *               |
     *               E
     */
    public function testGetInOrder()
    {
        $root = new Node('root');
        $root
            ->addChild($a = new Node('A'))
            ->addChild($b = new Node('B'));

        $a
            ->addChild($c = new Node('C'))
            ->addChild($d = new Node('D', [$e = new Node('E')]));

        $visitor = new InOrderVisitor();

        $yield = $root->accept($visitor);

        $this->assertSame([$c, $e, $b], $yield);
    }

    public function testTheInOrderOfALeafNodeIsTheNodeItself()
    {
        $node = new Node('node');
        $visitor = new InOrderVisitor;

        $this->assertSame([$node], $node->accept($visitor));
    }
}
