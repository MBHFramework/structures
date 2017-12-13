<?php namespace Mbh\Collection\Traits\Sequenceable;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use SplFixedArray;
use Traversable;

trait Builder
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
     * @inheritDoc
     */
    public static function fromArray(array $array)
    {
        return new static(SplFixedArray::fromArray($array));
    }

    /**
     * @inheritDoc
     */
    public static function fromItems(Traversable $array)
    {
        if (!$array instanceof Countable) {
            return static::fromArray(iterator_to_array($array));
        }

        $sfa = new SplFixedArray(count($array));

        foreach ($array as $i => $elem) {
            $sfa[$i] = $elem;
        }

        return new static($sfa);
    }

    abstract protected function checkCapacity();
}
