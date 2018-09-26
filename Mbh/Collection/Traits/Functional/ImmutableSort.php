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

trait ImmutableSort
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

        return SplFixedArray::fromArray($array);
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

        return SplFixedArray::fromArray($h->toArray());
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
        $sfa = $this->getValues();
        
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

    abstract public static function fromItems(Traversable $array);

    abstract public function copy();

    abstract public function count(): int;

    abstract public function toArray(): array;

    abstract protected function getValues(): Traversable;
}
