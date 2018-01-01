<?php namespace Mbh\Collection\Traits\Sequenceable\Arrayed;

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
    protected $sfa = null;

    /**
     * Countable
     */
    public function count(): int
    {
        return count($this->sfa);
    }
}
