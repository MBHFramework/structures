<?php namespace Mbh\Collection;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\Interfaces\Collection as CollectionInterface;
use Mbh\Collection\Interfaces\Hashable as HashableInterface;
use Mbh\Collection\Interfaces\Sequenceable as SequenceableInterface;
use Traversable;
use ArrayAccess;
use IteratorAggregate;
use OutOfBoundsException;
use OutOfRangeException;

/**
 * A Map is a sequential collection of key-value pairs, almost identical to an
 * array used in a similar context. Keys can be any type, but must be unique.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */
class Set implements ArrayAccess, CollectionInterface, IteratorAggregate
{
    use Traits\Collection;
    use Traits\Functional;
    use Traits\SquaredCapacity;

    const MIN_CAPACITY = 8.0;

    /**
     * @var FixedArray internal array to store pairs
     */
    private $pairs;

    /**
     * Creates a new instance.
     *
     * @param array|Traversable $pairs
     */
    public function __construct($pairs = [])
    {
        FixedArray::fromArray([]);

        $this->pushAll($pairs);
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        $this->pairs->clear();
        $this->capacity = self::MIN_CAPACITY;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->pairs);
    }

    /**
     * Returns whether an association a given key exists.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function hasKey($key): bool
    {
        return $this->lookupKey($key) !== null;
    }

    /**
     * Returns whether an association for a given value exists.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function hasValue($value): bool
    {
        return $this->lookupValue($value) !== null;
    }

    /**
     * Returns a set of all the keys in the map.
     *
     * @return Set
     */
    public function keys(): Set
    {
        return new static($this->pairs->map(function ($pair) {
            return $pair->key;
        }));
    }

    /**
     * Determines whether two keys are equal.
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return bool
     */
    private function keysAreEqual($a, $b): bool
    {
        if (is_object($a) && $a instanceof HashableInterface) {
            return get_class($a) === get_class($b) && $a->equals($b);
        }

        return $a === $b;
    }

    /**
     * Attempts to look up a key in the table.
     *
     * @param $key
     *
     * @return Pair|null
     */
    private function lookupKey($key)
    {
        foreach ($this->pairs as $pair) {
            if ($this->keysAreEqual($pair->key, $key)) {
                return $pair;
            }
        }
    }

    /**
     * Attempts to look up a key in the table.
     *
     * @param $value
     *
     * @return Pair|null
     */
    private function lookupValue($value)
    {
        foreach ($this->pairs as $pair) {
            if ($pair->value === $value) {
                return $pair;
            }
        }
    }

    /**
     * Associates a key with a value, replacing a previous association if there
     * was one.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function put($key, $value)
    {
        $pair = $this->lookupKey($key);
        if ($pair) {
            $pair->value = $value;
        } else {
            $this->checkCapacity();
            $this->pairs[] = new Pair($key, $value);
        }
    }

    /**
     * Creates associations for all keys and corresponding values of either an
     * array or iterable object.
     *
     * @param Traversable|array $values
     */
    public function putAll($values)
    {
        foreach ($values as $key => $value) {
            $this->put($key, $value);
        }
    }

    /**
     * Returns a sequence of all the associated values in the Map.
     *
     * @return SequenceableInterface
     */
    public function values(): SequenceableInterface
    {
        return $this->pairs->map(function ($pair) {
            return $pair->value;
        });
    }
}
