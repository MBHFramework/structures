<?php namespace Mbh\Tree\Interfaces;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Tree\Interfaces\Node as NodeInterface;

/**
 * Builder interface
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */
interface Builder
{
    /**
     * Set the node the builder will manage
     *
     * @param NodeInterface $node
     *
     * @return Builder The current instance
     */
    public function setNode(NodeInterface $node);

    /**
     * Get the node the builder manages
     *
     * @return NodeInterface
     */
    public function getNode();

    /**
     * Set the value of the underlaying node
     *
     * @param mixed $value
     *
     * @return Builder The current instance
     */
    public function value($value);

    /**
     * Add a leaf to the node
     *
     * @param mixed $value The value of the leaf node
     *
     * @return Builder The current instance
     */
    public function leaf($value = null);

    /**
     * Add several leafs to the node
     *
     * @param $value, ... An arbitrary long list of values
     *
     * @return Builder The current instance
     */
    public function leafs($value);

    /**
     * Add a child to the node enter in its scope
     *
     * @param null $value
     *
     * @return Builder A Builder instance linked to the child node
     */
    public function tree($value);

    /**
     * Goes up to the parent node context
     *
     * @return null|Builder A Builder instanced linked to the parent node
     */
    public function end();

    /**
     * Return a node instance set with the given value. Implementation can follow their own logic
     * in choosing the NodeInterface implmentation taking into account the value
     *
     * @param mixed $value
     *
     * @return NodeInterface
     */
    public function nodeInstanceByValue($value = null);
}
