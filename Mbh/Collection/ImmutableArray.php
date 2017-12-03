<?php namespace Mbh\Collection;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\Interfaces\Sequenceable as SequenceableInterface;
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

class ImmutableArray implements SequenceableInterface
{
    use Traits\Sequenceable;

    // The secondary flash array - fixed array
    private $sfa = null;

    /**
     * Create an immutable array
     *
     * @param Traversable $immute data guaranteed to be immutable
     */
    private function __construct(Traversable $immute)
    {
        $this->sfa = $immute;
    }

    /**
     * Factory for building ImmutableArrays from any traversable
     *
     * @return ImmutableArray
     */
    public static function fromItems(Traversable $array): self
    {
        // We can only do it this way if we can count it
        if ($array instanceof Countable) {
            $sfa = new SplFixedArray(count($array));

            foreach ($array as $i => $elem) {
                $sfa[$i] = $elem;
            }

            return new static($sfa);
        }

        // If we can't count it, it's simplest to iterate into an array first
        return static::fromArray(iterator_to_array($array));
    }

    /**
     * Build from an array
     *
     * @return ImmutableArray
     */
    public static function fromArray(array $array): self
    {
        return new static(SplFixedArray::fromArray($array));
    }

    public function toArray(): array
    {
        return $this->sfa->toArray();
    }

    /**
     * Countable
     */
    public function count(): int
    {
        return count($this->sfa);
    }

    /**
     * Iterator
     */
    public function current()
    {
        return $this->sfa->current();
    }

    public function key(): int
    {
        return $this->sfa->key();
    }

    public function next()
    {
        return $this->sfa->next();
    }

    public function rewind()
    {
        return $this->sfa->rewind();
    }

    public function valid()
    {
        return $this->sfa->valid();
    }

    /**
     * ArrayAccess
     */
    public function offsetExists($offset): bool
    {
        return $this->sfa->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->sfa->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Attempt to mutate immutable ' . __CLASS__ . ' object.');
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException('Attempt to mutate immutable ' . __CLASS__ . ' object.');
    }

    public function clear()
    {
        throw new RuntimeException('Attempt to mutate immutable ' . __CLASS__ . ' object.');
    }
}
