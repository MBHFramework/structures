<?php namespace Mbh\Tree\Interfaces;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

/**
 * Node interface
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */
interface Node
{
    /**
     * Set the value of the current node
     *
     * @param mixed $value
     *
     * @return Node the current instance
     */
    public function setValue($value);

    /**
     * Get the current node value
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Add a child
     *
     * @param Node $child
     *
     * @return mixed
     */
    public function addChild(Node $child);

    /**
     * Remove a node from children
     *
     * @param Node $child
     *
     * @return Node the current instance
     */
    public function removeChild(Node $child);

    /**
     * Remove all children
     *
     * @return Node The current instance
     */
    public function removeAllChildren();

    /**
     * Return the array of children
     *
     * @return Node[]
     */
    public function getChildren();

    /**
     * Replace the children set with the given one
     *
     * @param Node[] $children
     *
     * @return mixed
     */
    public function setChildren(array $children);

    /**
     * setParent
     *
     * @param Node $parent
     * @return void
     */
    public function setParent(Node $parent = null);

    /**
     * getParent
     *
     * @return Node
     */
    public function getParent();

    /**
     * Retrieves all ancestors of node excluding current node.
     *
     * @return array
     */
    public function getAncestors();

    /**
     * Retrieves all ancestors of node as well as the node itself.
     *
     * @return Node[]
     */
    public function getAncestorsAndSelf();

    /**
     * Retrieves all neighboring nodes, excluding the current node.
     *
     * @return array
     */
    public function getNeighbors();

    /**
     * Returns all neighboring nodes, including the current node.
     *
     * @return Node[]
     */
    public function getNeighborsAndSelf();

    /**
     * Return true if the node is the root, false otherwise
     *
     * @return bool
     */
    public function isRoot();

    /**
     * Return true if the node is a child, false otherwise.
     *
     * @return bool
     */
    public function isChild();

    /**
     * Return true if the node has no children, false otherwise
     *
     * @return bool
     */
    public function isLeaf();

    /**
     * Return the distance from the current node to the root
     *
     * @return int
     */
    public function getDepth();

    /**
     * Return the height of the tree whose root is this node
     *
     * @return int
     */
    public function getHeight();

    /**
     * Return the number of nodes in a tree
     * @return int
     */
    public function getSize();

    /**
     * Accept method for the visitor pattern (see http://en.wikipedia.org/wiki/Visitor_pattern)
     *
     * @param Visitor $visitor
     * @return void
     */
    public function accept(Visitor $visitor);
}
