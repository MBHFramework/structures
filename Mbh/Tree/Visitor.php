<?php namespace Mbh\Tree;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Tree\Interfaces\Node as NodeInterface;
use Mbh\Tree\Interfaces\Visitor as VisitorInterface;

abstract class Visitor implements VisitorInterface
{
    /**
     * @param Node $node
     * @return mixed
     */
    abstract public function visit(NodeInterface $node);
}
