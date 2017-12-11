<?php namespace Mbh\Tree\Traits;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Tree\Interfaces\Node as NodeInterface;

trait Builder
{
    /**
     * {@inheritdoc}
     */
    public function setNode(NodeInterface $node)
    {
        $this->emptyStack()->pushNode($node);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNode()
    {
        return $this->peekStack();
    }

    /**
     * {@inheritdoc}
     */
    public function leaf($value = null)
    {
        $this->getNode()->addChild(
            $this->nodeInstanceByValue($value)
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function leafs($value1 /*,  $value2, ... */)
    {
        foreach (func_get_args() as $value) {
            $this->leaf($value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function tree($value)
    {
        $node = $this->nodeInstanceByValue($value);
        $this->getNode()->addChild($node);
        $this->pushNode($node);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function end()
    {
        $this->popNode();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function nodeInstanceByValue($value = null)
    {
        return new \Mbh\Tree\Node($value);
    }

    /**
     * {@inheritdoc}
     */
    public function value($value = null)
    {
        $this->getNode()->setValue($value);
        return $this;
    }

    abstract protected function peekStack(): NodeInterface;

    abstract protected function emptyStack();

    abstract protected function pushNode(NodeInterface $node);

    abstract protected function popNode();
}
