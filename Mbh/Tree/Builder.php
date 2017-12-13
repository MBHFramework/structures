<?php namespace Mbh\Tree;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Tree\Interfaces\Node as NodeInterface;
use Mbh\Tree\Interfaces\Builder as BuilderInterface;
use Mbh\Collection\FixedArray;

class Builder
{
    use \Mbh\Tree\Traits\Builder;

    /**
     * @var FixedArray
     */
    protected $nodeStack = null;

    /**
     * @param NodeInterface $node
     */
    public function __construct(NodeInterface $node = null)
    {
        $this->nodeStack = FixedArray::fromArray([]);
        $this->setNode($node ?: $this->nodeInstanceByValue());
    }

    protected function peekStack(): NodeInterface
    {
        return $this->nodeStack->last();
    }

    protected function emptyStack()
    {
        $this->nodeStack->clear();
        return $this;
    }

    protected function pushNode(NodeInterface $node)
    {
        $this->nodeStack->push($node);
        return $this;
    }

    protected function popNode()
    {
        return $this->nodeStack->pop();
    }
}
