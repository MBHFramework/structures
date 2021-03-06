<?php namespace Mbh\Collection\Traits\Functional;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\Interfaces\Collection as CollectionInterface;
use Mbh\Collection\FixedArray;
use Mbh\Collection\CallbackHeap;
use Traversable;
use SplFixedArray;
use SplStack;
use LimitIterator;

trait InPlaceSort
{
    /**
     * Fallback behaviour to use the builtin array sort functions
     *
     * @param callable $callback The callback for comparison
     * @return CollectionInterface
     */
    public function arraySort(callable $callback = null)
    {
        $array = $this->toArray();

        if ($callback) {
            usort($array, $callback);
        } else {
            sort($array);
        }

        $this->setValues(SplFixedArray::fromArray($array));

        return $this;
    }

    /**
     * Sort by applying a CallbackHeap and building a new heap
     * Can be efficient for sorting large stored objects.
     *
     * @param callable $callback The comparison callback
     * @return CollectionInterface
     */
    public function heapSort(callable $callback)
    {
        $h = new CallbackHeap($callback);
        foreach ($this as $elem) {
            $h->insert($elem);
        }

        $this->setValues(SplFixedArray::fromArray($h->toArray()));

        return $this;
    }

    /**
     * Perform a bottom-up, non-recursive, in-place mergesort.
     * Efficient for very-large objects, and written without recursion
     * since PHP isn't well optimized for large recursion stacks.
     *
     * @param callable $callback The callback for comparison
     * @return CollectionInterface
     */
    public function mergeSort(callable $callback)
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

    abstract public static function fromItems(Traversable $array);

    abstract public function copy();

    abstract public function count(): int;

    abstract protected function setValues(Traversable $traversable);

    abstract public function toArray(): array;
}
