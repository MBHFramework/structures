<?php namespace Mbh\Collection\Traits\Sequenceable;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Traversable;

trait Iterator
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
     * Iterator
     */
    public function current()
    {
        return $this->sfa->current();
    }

    public function key(): int
    {
        return $this->sfa->key();
    }

    public function next()
    {
        return $this->sfa->next();
    }

    public function rewind()
    {
        return $this->sfa->rewind();
    }

    public function valid()
    {
        return $this->sfa->valid();
    }
}
