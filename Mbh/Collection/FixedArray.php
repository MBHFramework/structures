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
use Mbh\Traits\Capacity;

/**
 * The Fixed Array
 *
 * A FixedArray is a sequence of values in a contiguous buffer that grows and
 * shrinks automatically. It’s the most efficient sequential structure because
 * a value’s index is a direct mapping to its index in the buffer, and the
 * growth factor isn't bound to a specific multiple or exponent.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */
class FixedArray implements AllocatedInterface, FunctionalInterface, SequenceableInterface
{
    use Traits\Collection;
    use Traits\Sequenceable;
    use Traits\Functional;
    use Capacity;
    use EmptyGuard;

    const MIN_CAPACITY = 8.0;

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
