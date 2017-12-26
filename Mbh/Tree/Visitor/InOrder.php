<?php namespace Mbh\Tree\Visitor;

use Mbh\Tree\Interfaces\Node as NodeInterface;
use Mbh\Tree\Visitor;

class InOrder extends Visitor
{
    public function visit(NodeInterface $node)
    {
        if ($node->isLeaf()) {
            return [$node];
        }

        $yield = [];

        foreach ($node->getChildren() as $child) {
            $yield = array_merge($yield, $child->accept($this));
        }

        return $yield;
    }
}
