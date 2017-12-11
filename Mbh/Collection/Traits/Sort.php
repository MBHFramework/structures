<?php namespace Mbh\Collection\Traits;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\FixedArray;
use Mbh\Collection\Interfaces\Sequenceable as SequenceableInterface;
use Traversable;
use SplFixedArray;
use SplStack;
use LimitIterator;

trait Sort
{
    /**
     * Perform a bottom-up, non-recursive, in-place mergesort.
     * Efficient for very-large objects, and written without recursion
     * since PHP isn't well optimized for large recursion stacks.
     *
     * @param callable $callback The callback for comparison
     * @return SequenceableInterface
     */
    public function mergeSort(callable $callback): SequenceableInterface
    {
        $count = $this->count();
        $result = new SplFixedArray($count);
        for ($k = 1; $k < $count; $k = $k << 1) {
            for ($left = 0; ($left + $k) < $count; $left += $k << 1) {
                $right = $left + $k;
                $rend = min($right + $k, $count);
                $m = $left;
                $i = $left;
                $j = $right;
                while ($i < $right && $j < $rend) {
                    if ($callback($this[$i], $this[$j]) <= 0) {
                        $result[$m] = $this[$i];
                        $i++;
                    } else {
                        $result[$m] = $this[$j];
                        $j++;
                    }
                    $m++;
                }
                while ($i < $right) {
                    $result[$m] = $this[$i];
                    $i++;
                    $m++;
                }
                while ($j < $rend) {
                    $result[$m] = $this[$j];
                    $j++;
                    $m++;
                }
                for ($m = $left; $m < $rend; $m++) {
                    $this[$m] = $result[$m];
                }
            }
        }

        return $this;
    }

    /**
     * Sort by applying a CallbackHeap and building a new heap
     * Can be efficient for sorting large stored objects.
     *
     * @param callable $callback The comparison callback
     * @return SequenceableInterface
     */
    public function heapSort(callable $callback): SequenceableInterface
    {
        $h = new CallbackHeap($callback);
        foreach ($this as $elem) {
            $h->insert($elem);
        }

        $this->setTraversable(SplFixedArray::fromItems($h));

        return $this;
    }

    /**
     * Fallback behaviour to use the builtin array sort functions
     *
     * @param callable $callback The callback for comparison
     * @return SequenceableInterface
     */
    public function arraySort(callable $callback = null): SequenceableInterface
    {
        $array = $this->toArray();

        if ($callback) {
            usort($array, $callback);
        } else {
            sort($array);
        }

        $this->setTraversable(SplFixedArray::fromArray($array));

        return $this;
    }

    /**
     * Sorts the collection with mergeSort
     *
     * @param callable $callback The callback for comparison
     * @return SequenceableInterface
     */
    public function mergeSorted(callable $callback = null): SequenceableInterface
    {
        return $this->copy()->mergeSort($callback);
    }

    /**
     * Sort by applying a CallbackHeap and building a new heap
     * Can be efficient for sorting large stored objects.
     *
     * @param callable $callback The comparison callback
     * @return SequenceableInterface
     */
    public function heapSorted(callable $callback): SequenceableInterface
    {
        return $this->copy()->heapSort($callback);
    }

    /**
     * Sorts the collection
     *
     * @param callable $callback The callback for comparison
     * @return SequenceableInterface
     */
    public function arraySorted(callable $callback = null): SequenceableInterface
    {
        return $this->copy()->arraySort($callback);
    }

    abstract protected function setTraversable(Traversable $traversable);

    abstract public function count(): int;
}
