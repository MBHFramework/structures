<?php namespace Mbh\Collection\Traits;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

trait Capacity
{
    /**
     * Returns the current capacity.
     *
     * @return int
     */
    public function capacity(): int
    {
        return $this->getSize();
    }

    /**
     * Ensures that enough memory is allocated for a specified capacity. This
     * potentially reduces the number of reallocations as the size increases.
     *
     * @param int $capacity The number of values for which capacity should be
     *                      allocated. Capacity will stay the same if this value
     *                      is less than or equal to the current capacity.
     */
    public function allocate(int $capacity)
    {
        $this->setSize(max($capacity, $this->getSize()));
    }

    /**
     * @return the structures growth factor.
     */
    protected function getGrowthFactor(): float
    {
        return 2;
    }

    /**
     * @return float to multiply by when decreasing capacity.
     */
    protected function getDecayFactor(): float
    {
        return 0.5;
    }

    /**
     * @return float the ratio between size and capacity when capacity should be
     *               decreased.
     */
    protected function getTruncateThreshold(): float
    {
        return 0.25;
    }

    /**
     * Checks and adjusts capacity if required.
     */
    protected function checkCapacity()
    {
        if ($this->shouldIncreaseCapacity()) {
            $this->increaseCapacity();
        } else {
            if ($this->shouldDecreaseCapacity()) {
                $this->decreaseCapacity();
            }
        }
    }

    /**
     * Called when capacity should be increased to accommodate new values.
     */
    protected function increaseCapacity()
    {
        $this->setSize(max($this->count(), $this->getSize() * $this->getGrowthFactor()));
    }

    /**
     * Called when capacity should be decrease if it drops below a threshold.
     */
    protected function decreaseCapacity()
    {
        $this->setSize(max(self::MIN_CAPACITY, $this->getSize()  * $this->getDecayFactor()));
    }

    /**
     * @return whether capacity should be increased.
     */
    protected function shouldDecreaseCapacity(): bool
    {
        return count($this) <= $this->getSize() * $this->getTruncateThreshold();
    }

    /**
     * @return whether capacity should be increased.
     */
    protected function shouldIncreaseCapacity(): bool
    {
        return count($this) >= $this->getSize();
    }

    /**
     * Gets the size of the array.
     *
     * @return int
     */
    abstract public function getSize(): int;

    /**
     * Change the size of an array to the new size of size.
     * If size is less than the current array size, any values after the
     * new size will be discarded. If size is greater than the current
     * array size, the array will be padded with NULL values.
     *
     * @param int $size The new array size. This should be a value between 0
     * and PHP_INT_MAX.
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    abstract public function setSize(int $size): bool;
}
