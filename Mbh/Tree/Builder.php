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

class Builder
{
    use \Mbh\Tree\Traits\Builder;

    /**
     * @var NodeInterface[]
     */
    protected $nodeStack = [];

    /**
     * @param NodeInterface $node
     */
    public function __construct(NodeInterface $node = null)
    {
        $this->setNode($node ?: $this->nodeInstanceByValue());
    }

    protected function peekStack(): NodeInterface
    {
        return $this->nodeStack[count($this->nodeStack) - 1];
    }

    protected function emptyStack()
    {
        $this->nodeStack = [];
        return $this;
    }

    protected function pushNode(NodeInterface $node)
    {
        array_push($this->nodeStack, $node);
        return $this;
    }

    protected function popNode()
    {
        return array_pop($this->nodeStack);
    }
}
