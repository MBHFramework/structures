<?php namespace Mbh\Collection\Internal;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\Internal\Interfaces\LinkedNode;

/**
 * @internal
 */
class LinkedDataNode implements LinkedNode
{
    private $prev;
    private $value;
    private $next;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function value()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return LinkedNode
     */
    public function prev()
    {
        return $this->prev;
    }

    /**
     * @return LinkedNode
     */
    public function next()
    {
        return $this->next;
    }

    public function setPrev(LinkedNode $prev)
    {
        $this->prev = $prev;
    }

    public function setNext(LinkedNode $next)
    {
        $this->next = $next;
    }
}
