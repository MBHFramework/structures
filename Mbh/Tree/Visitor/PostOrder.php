<?php namespace Mbh\Tree\Visitor;

use Mbh\Tree\Interfaces\Node as NodeInterface;
use Mbh\Tree\Visitor;

class PostOrder extends Visitor
{
    public function visit(NodeInterface $node)
    {
        $nodes = [];
        foreach ($node->getChildren() as $child) {
            $nodes = array_merge(
                $nodes,
                $child->accept($this)
            );
        }

        $nodes[] = $node;

        return $nodes;
    }
}
