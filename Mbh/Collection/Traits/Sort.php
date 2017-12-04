<?php namespace Mbh\Collection\Traits;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

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
     * @return ImmutableArray
     */
    public function mergeSort(callable $callback)
    {
        $count = count($this);
        $sfa = $this->sfa;
        $result = new SplFixedArray($count);
        for ($k = 1; $k < $count; $k = $k << 1) {
            for ($left = 0; ($left + $k) < $count; $left += $k << 1) {
                $right = $left + $k;
                $rend = min($right + $k, $count);
                $m = $left;
                $i = $left;
                $j = $right;
                while ($i < $right && $j < $rend) {
                    if ($callback($sfa[$i], $sfa[$j]) <= 0) {
                        $result[$m] = $sfa[$i];
                        $i++;
                    } else {
                        $result[$m] = $sfa[$j];
                        $j++;
                    }
                    $m++;
                }
                while ($i < $right) {
                    $result[$m] = $sfa[$i];
                    $i++;
                    $m++;
                }
                while ($j < $rend) {
                    $result[$m] = $sfa[$j];
                    $j++;
                    $m++;
                }
                for ($m = $left; $m < $rend; $m++) {
                    $sfa[$m] = $result[$m];
                }
            }
        }

        return new static($sfa);
    }

    /**
     * A classic quickSort - great for inplace sorting a big fixed array
     *
     * @param callable $callback The callback for comparison
     * @return ImmutableArray
     */
    public function quickSort(callable $callback): self
    {
        $sfa = new SplFixedArray(count($this));

        // Create an auxiliary stack
        $stack = new SplStack();

        // initialize top of stack
        // push initial values of l and h to stack
        $stack->push([0, count($sfa) - 1]);

        $first = true;

        // Keep popping from stack while is not empty
        while (!$stack->isEmpty()) {
            // Pop h and l
            list($lo, $hi) = $stack->pop();
            if ($first) {
                // Start our partition iterator on the original data
                $partition = new LimitIterator($this, $lo, $hi - $lo);
            } else {
                $partition = new LimitIterator($sfa, $lo, $hi - $lo);
            }
            $ii = $partition->getInnerIterator();
            // Set pivot element at its correct position in sorted array
            $x = $ii[$hi];
            $i = ($lo - 1);
            foreach ($partition as $j => $elem) {
                if ($callback($ii[$j], $x) <= 0) {
                    // Bump up the index of the last low hit, and swap
                    $i++;
                    $temp = $sfa[$i];
                    $sfa[$i] = $elem;
                    $sfa[$j] = $temp;
                } elseif ($first) {
                    $sfa[$j] = $elem;
                    $first = false;
                }
            }

            // Set the pivot element
            $pivot = $i + 1;

            // Swap the last hi with the second-last hi
            $sfa[$hi] = $sfa[$pivot];
            $sfa[$pivot] = $x;

            // If there are elements on left side of pivot, then push left
            // side to stack
            if ($pivot - 1 > $lo) {
                $stack->push([$lo, $pivot - 1]);
            }
            // If there are elements on right side of pivot, then push right
            // side to stack
            if ($pivot + 1 < $hi) {
                $stack->push([$pivot + 1, $hi]);
            }
        }

        return new static($sfa);
    }

    /**
     * Sort by applying a CallbackHeap and building a new heap
     * Can be efficient for sorting large stored objects.
     *
     * @param callable $callback The comparison callback
     * @return ImmutableArray
     */
    public function heapSort(callable $callback): self
    {
        $h = new CallbackHeap($callback);
        foreach ($this as $elem) {
            $h->insert($elem);
        }

        return static::fromItems($h);
    }

    /**
     * Fallback behaviour to use the builtin array sort functions
     *
     * @param callable $callback The callback for comparison
     * @return ImmutableArray
     */
    public function arraySort(callable $callback = null)
    {
        $array = $this->toArray();

        if ($callback) {
            usort($array, $callback);
        } else {
            sort($array);
        }

        return static::fromArray($array);
    }
}
