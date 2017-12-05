<?php namespace Mbh\Collection;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\Interfaces\Collection as CollectionInterface;
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
 * The Fixed Array
 *
 * A FixedArray is a sequence of values in a contiguous buffer that grows and
 * shrinks automatically. It’s the most efficient sequential structure because
 * a value’s index is a direct mapping to its index in the buffer, and the
 * growth factor isn't bound to a specific multiple or exponent.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */

class FixedArray implements SequenceableInterface
{
    use Traits\Sequenceable;

    // The secondary flash array - fixed array
    protected $sfa = null;

    /**
     * Create an fixed array
     *
     * @param Traversable $fixed data guaranteed to be immutable
     */
    protected function __construct(Traversable $fixed)
    {
        $this->sfa = $fixed;
    }

    public function toArray(): array
    {
        return $this->sfa->toArray();
    }

    private function validIndex(int $index)
    {
        return $index >= 0 && $index < count($this);
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
        return is_integer($offset)
            && $this->validIndex($offset)
            && $this->sfa->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->sfa->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        return is_integer($offset)
            && $this->validIndex($offset)
            && $this->sfa->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return is_integer($offset)
            && $this->validIndex($offset)
            && $this->sfa->offsetUnset($offset);
    }

    public function clear()
    {
        return $this->sfa->clear();
    }
}
