<?php namespace Mbh\Collection\Internal\Interfaces;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

/**
 * @internal
 */
interface LinkedNode
{
    /**
     * @return LinkedNode
     */
    public function prev();

    /**
     * @return LinkedNode
     */
    public function next();

    public function setPrev(LinkedNode $prev);
    
    public function setNext(LinkedNode $next);
}
