<?php namespace Mbh\Collection;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\CallbackHeap;
use Mbh\Iterator\SliceIterator;
use Mbh\Iterator\ConcatIterator;
use SplFixedArray;
use SplHeap;
use SplStack;
use LimitIterator;
use Iterator;
use ArrayAccess;
use Countable;
use CallbackFilterIterator;
use JsonSerializable;
use RuntimeException;
use Traversable;
use ReflectionClass;

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

class ImmutableArray implements Iterator, ArrayAccess, Countable, JsonSerializable
{
    use SortTrait {
        SortTrait::quickSort as quickSortWithCallback;
    }

    // The secondary flash array - fixed array
    private $sfa = null;

    /**
     * Create an immutable array
     *
     * @param Traversable $immute Data guaranteed to be immutable
     */
    private function __construct(Traversable $immute)
    {
        $this->sfa = $immute;
    }

    /**
     * Map elements to a new ImmutableArray via a callback
     *
     * @param callable $callback Function to map new data
     * @return ImmutableArray
     */
    public function map(callable $callback): self
    {
        $count = count($this);
        $sfa = new SplFixedArray($count);

        for ($i = 0; $i < $count; $i++) {
            $sfa[$i] = $callback($this->sfa[$i], $i, $this);
        }

        return new static($sfa);
    }

    /**
     * forEach, or "walk" the data
     * Exists primarily to provide a consistent interface, though it's seldom
     * any better than a simple php foreach. Mainly useful for chaining.
     * Named walk for historic reasons - forEach is reserved in PHP
     *
     * @param callable $callback Function to call on each element
     * @return ImmutableArray
     */
    public function walk(callable $callback): self
    {
        foreach ($this as $i => $elem) {
            $callback($elem, $i, $this);
        }

        return $this;
    }

    /**
     * Filter out elements
     *
     * @param callable $callback Function to filter out on false
     * @return ImmutableArray
     */
    public function filter(callable $callback): self
    {
        $count = count($this->sfa);
        $sfa = new SplFixedArray($count);
        $newCount = 0;

        foreach ($this->sfa as $elem) {
            if ($callback($elem)) {
                $sfa[$newCount++] = $elem;
            }
        }

        $sfa->setSize($newCount);
        return new static($sfa);
    }

    /**
     * Reduce to a single value
     *
     * @param callable $callback Callback(
     *     mixed $previous, mixed $current[, mixed $index, mixed $immArray]
     * ):mixed Callback to run reducing function
     * @param mixed $accumulator Initial value for first argument
     */
    public function reduce(callable $callback, $accumulator = null)
    {
        foreach ($this->sfa as $i => $elem) {
            $accumulator = $callback($accumulator, $elem, $i, $this);
        }

        return $accumulator;
    }

    /**
     * Take a slice of the array
     *
     * @param int $begin Start index of slice
     * @param int $end End index of slice
     * @return ImmutableArray
     */
    public function slice(int $begin = 0, int $end = null): self
    {
        $it = new SliceIterator($this->sfa, $begin, $end);
        return new static($it);
    }

    /**
     * Concat to the end of this array
     *
     * @param Traversable,...
     * @return ImmutableArray
     */
    public function concat(...$args): self
    {
        $concatIt = new ConcatIterator(...$args);
        return new static($concatIt);
    }
}
