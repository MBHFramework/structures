<?php namespace Mbh\Collection;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\Interfaces\Functional as FunctionalInterface;
use Mbh\Collection\Interfaces\Sequenceable as SequenceableInterface;
use Mbh\Interfaces\Allocated as AllocatedInterface;
use Mbh\Exceptions\ImmutableException;
use Mbh\Traits\Capacity;
use Mbh\Traits\EmptyGuard;

/**
 * The Immutable Array
 *
 * This provides special methods for quickly creating an immutable array,
 * either from any Traversable, or using a C-optimized fromArray() to directly
 * instantiate from. Also includes methods fundamental to functional
 * programming, e.g. map, filter, join, and sort.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */
class ImmutableArray implements AllocatedInterface, FunctionalInterface, SequenceableInterface
{
    use Capacity;
    use EmptyGuard;
    use Traits\Collection;
    use Traits\ImmutableFunctional;
    use Traits\Sequenceable;
    use Traits\Sequenceable\Arrayed;

    const MIN_CAPACITY = 8.0;

    /**
     * ArrayAccess
     */
    public function offsetExists($offset)
    {
        return $this->sfa->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->sfa->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw ImmutableException::cannotModify(__CLASS__, __METHOD__);
    }

    public function offsetUnset($offset)
    {
        throw ImmutableException::cannotModify(__CLASS__, __METHOD__);
    }

    /**
     * @inheritDoc
     */
    protected function getGrowthFactor(): float
    {
        return 1.5;
    }

    /**
     * @inheritDoc
     */
    protected function shouldIncreaseCapacity(): bool
    {
        return count($this) > $this->getSize();
    }
}
