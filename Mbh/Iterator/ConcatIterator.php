<?php namespace Mbh\Iterator;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use ArrayAccess;
use AppendIterator;
use JsonSerializable;
use Countable;
use Iterator;
use RuntimeException;
use InvalidArgumentException;

/**
 * Iterator to allow multiple iterators to be concatenated
 */

class ConcatIterator extends AppendIterator implements ArrayAccess, Countable, JsonSerializable
{
    const INVALID_INDEX = 'Index invalid or out of range';

    /** @var int $count Fast-lookup count for full set of iterators */
    public $count = 0;

    /**
     * Build an iterator over multiple iterators
     * Unlike a LimitIterator, the $end defines the last index, not the count
     *
     * @param Iterator $iterator,... Concat iterators in order
     */
    public function __construct(...$args)
    {
        parent::__construct();

        foreach ($args as $i => $iterator) {
            if (!(
                $iterator instanceof ArrayAccess
                && $iterator instanceof Countable
            )) {
                throw new InvalidArgumentException(
                    'Argument ' . $i .
                    ' passed to ' . __METHOD__ .
                    ' must be of type ArrayAccess, Countable, and Traversable. ' .
                    gettype($iterator) . ' given.'
                );
            }

            // Unroll other ConcatIterators, so we avoid deep iterator stacks
            if ($iterator instanceof self) {
                foreach ($iterator as $innerIt) {
                    $this->append($innerIt);
                }
            } else {
                $this->append($iterator);
            }

            $this->count += count($iterator);
        }
    }

    /**
     * Countable
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * ArrayAccess
     */
    public function offsetExists($offset): bool
    {
        return $offset >= 0 && $offset < $this->count;
    }

    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new RuntimeException(self::INVALID_INDEX);
        }

        list($it, $idx) = $this->getIteratorByIndex($offset);
        return $it->offsetGet($idx);
    }

    public function offsetSet($offset, $value)
    {
        list($it, $idx) = $this->getIteratorByIndex($offset);
        $it->offsetSet($idx, $value);
    }

    public function offsetUnset($offset)
    {
        list($it, $idx) = $this->getIteratorByIndex($offset);
        $it->offsetUnset($idx);
    }

    /**
     * JsonSerializable
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return iterator_to_array($this, false);
    }

    /**
     * Find which of the inner iterators an index corresponds to
     *
     * @param int $index
     * @return array [ArrayAccess, int] The iterator and interior index
     */
    protected function getIteratorByIndex($index = 0)
    {
        $runningCount = 0;
        
        foreach ($this as $innerIt) {
            $count = count($innerIt);
            if ($index < $runningCount + $count) {
                return [$innerIt, $index - $runningCount];
            }

            $runningCount += $count;
        }

        return null;
    }
}
