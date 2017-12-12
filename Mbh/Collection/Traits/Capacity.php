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
    protected $capacity = self::MIN_CAPACITY;

    /**
     * @inheritDoc
     */
    public function capacity(): int
    {
        return $this->capacity;
    }

    /**
     * @inheritDoc
     */
    public function allocate(int $capacity)
    {
        return $this->setSize($this->capacity = max($capacity, $this->capacity));
    }

    /**
     * @return float The structures growth factor.
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
     * @return float The ratio between size and capacity when capacity should be
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
        $this->allocate(max($this->count(), $this->capacity * $this->getGrowthFactor()));
    }

    /**
     * Called when capacity should be decrease if it drops below a threshold.
     */
    protected function decreaseCapacity()
    {
        $this->allocate(max(self::MIN_CAPACITY, $this->capacity * $this->getDecayFactor()));
    }

    /**
     * @return bool whether capacity should be increased.
     */
    protected function shouldDecreaseCapacity(): bool
    {
        return $this->count() <= $this->capacity * $this->getTruncateThreshold();
    }

    /**
     * @return bool whether capacity should be increased.
     */
    protected function shouldIncreaseCapacity(): bool
    {
        return $this->count() >= $this->capacity;
    }

    /**
     * Gets the size of the array.
     *
     * @return int
     */
    abstract protected function count(): int;

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
    abstract protected function setSize(int $size): bool
}
