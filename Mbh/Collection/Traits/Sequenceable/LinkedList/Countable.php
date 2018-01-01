<?php namespace Mbh\Collection\Traits\Sequenceable\LinkedList;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Traversable;

trait Countable
{
    /**
     * @link http://php.net/manual/en/countable.count.php
     * @return int
     */
    public function count(): int
    {
        return $this->getSize();
    }

    abstract protected function getSize();
}
