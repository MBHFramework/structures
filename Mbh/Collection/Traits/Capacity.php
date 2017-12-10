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
     * @var integer internal capacity
     */
    private $capacity = self::MIN_CAPACITY;

    /**
     * Returns the current capacity.
     *
     * @return int
     */
    public function capacity(): int
    {
        return $this->capacity;
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
        $this->capacity = max($capacity, $this->capacity);
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
        $this->capacity = max($this->count(), $this->capacity * $this->getGrowthFactor());
    }

    /**
     * Called when capacity should be decrease if it drops below a threshold.
     */
    protected function decreaseCapacity()
    {
        $this->capacity = max(self::MIN_CAPACITY, $this->capacity  * $this->getDecayFactor());
    }

    /**
     * @return whether capacity should be increased.
     */
    protected function shouldDecreaseCapacity(): bool
    {
        return count($this) <= $this->capacity * $this->getTruncateThreshold();
    }

    /**
     * @return whether capacity should be increased.
     */
    protected function shouldIncreaseCapacity(): bool
    {
        return count($this) >= $this->capacity;
    }
}
