<?php namespace Mbh\Collection\Traits\Sequenceable;

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
     * Create an fixed array
     *
     * @param Traversable $array data
     */
    protected function __construct(Traversable $array)
    {
        $this->sfa = $array;
        $this->checkCapacity();
    }

    /**
     * Countable
     */
    public function count(): int
    {
        return count($this->sfa);
    }
}
