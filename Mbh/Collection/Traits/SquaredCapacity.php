<?php namespace Mbh\Collection\Traits;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

    /**
     * Common to structures that require a capacity which is a power of two.
     */
trait SquaredCapacity
{
    use Capacity;

    /**
     * @var integer internal capacity
     */
    protected $capacity = self::MIN_CAPACITY;

    /**
     * Rounds an integer to the next power of two if not already a power of two.
     *
     * @param int $capacity
     *
     * @return int
     */
    private function square(int $capacity): int
    {
        return pow(2, ceil(log($capacity, 2)));
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
        $this->capacity = max($this->square($capacity), $this->capacity);
    }

    /**
     * Called when capacity should be increased to accommodate new values.
     */
    protected function increaseCapacity()
    {
        $this->capacity = $this->square(max($this->count() + 1, $this->capacity * $this->getGrowthFactor()));
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
    abstract protected function setSize(int $size): bool;
}
