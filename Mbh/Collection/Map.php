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
use Mbh\Collection\Internal\PriorityNode;
use Mbh\Interfaces\Allocated as AllocatedInterface;
use Mbh\Traits\SquaredCapacity;
use Mbh\Traits\EmptyGuard;
use Traversable;
use ArrayAccess;
use IteratorAggregate;
use OutOfBoundsException;
use OutOfRangeException;
use UnderflowException;

/**
 * A Map is a sequential collection of key-value pairs, almost identical to an
 * array used in a similar context. Keys can be any type, but must be unique.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */
class Map implements AllocatedInterface, ArrayAccess, CollectionInterface, IteratorAggregate
{
    use Traits\Collection;
    use Traits\Functional;
    use Traits\Builder;
    use SquaredCapacity;
    use EmptyGuard;

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
        $this->pairs = FixedArray::empty();

        $this->putAll($pairs);
    }

    /**
     * @throws OutOfBoundsException
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    /**
     * You should use this if you want to convert a n object into a map
     *
     * @param object $object
     * @return Map
     */
    public static function fromObject($object)
    {
        $payloadValue = get_object_vars($object);
        return static::fromArray($payloadValue);
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
     * Completely removes a pair from the internal array by position. It is
     * important to remove it from the array and not just use 'unset'.
     */
    private function delete(int $position)
    {
        $pair = $this->pairs->remove($position);

        $this->checkCapacity();
        return $pair->value;
    }

    /**
     * Return the first Pair from the Map
     *
     * @return Pair
     *
     * @throws UnderflowException
     */
    public function first(): Pair
    {
        $this->emptyGuard(__METHOD__);
        return $this->pairs->first();
    }

    /**
     * Returns the value associated with a key, or an optional default if the
     * key is not associated with a value.
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed The associated value or fallback default if provided.
     *
     * @throws OutOfBoundsException if no default was provided and the key is
     *                               not associated with a value.
     */
    public function get($key, $default = null)
    {
        if (($pair = $this->lookupKey($key))) {
            return $pair->value;
        }

        // Check if a default was provided.
        if (func_num_args() === 1) {
            throw new OutOfBoundsException();
        }

        return $default;
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
        return new Set($this->pairs->map(function($pair) {
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
     * Return the last Pair from the Map
     *
     * @return Pair
     *
     * @throws UnderflowException
     */
    public function last(): Pair
    {
        $this->emptyGuard(__METHOD__);
        return $this->pairs->last();
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
     * Returns a sequence of pairs representing all associations.
     *
     * @return SequenceableInterface
     */
    public function pairs(): SequenceableInterface
    {
        return $this->pairs->map(function($pair) {
            return $pair->copy();
        });
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
     * Removes a key's association from the map and returns the associated value
     * or a provided default if provided.
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed The associated value or fallback default if provided.
     *
     * @throws OutOfBoundsException if no default was provided and the key is
     *                               not associated with a value.
     */
    public function remove($key, $default = null)
    {
        foreach ($this->pairs as $position => $pair) {
            if ($this->keysAreEqual($pair->key, $key)) {
                return $this->delete($position);
            }
        }

        // Check if a default was provided
        if (func_num_args() === 1) {
            throw new OutOfBoundsException();
        }

        return $default;
    }

    /**
     * Return the pair at a specified position in the Map
     *
     * @param int $position
     *
     * @return Pair
     *
     * @throws OutOfRangeException
     */
    public function skip(int $position): Pair
    {
        if ($position < 0 || $position >= count($this->pairs)) {
            throw new OutOfRangeException();
        }

        return $this->pairs[$position]->copy();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $array = [];
        foreach ($this->pairs as $pair) {
            $array[$pair->key] = $pair->value;
        }

        return $array;
    }

    /**
     * Returns a sequence of all the associated values in the Map.
     *
     * @return SequenceableInterface
     */
    public function values(): SequenceableInterface
    {
        return $this->pairs->map(function($pair) {
            return $pair->value;
        });
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        foreach ($this->pairs as $pair) {
            yield $pair->key => $pair->value;
        }
    }

    /**
     * Returns a representation to be used for var_dump and print_r.
     */
    public function __debugInfo()
    {
        return $this->pairs()->toArray();
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->put($offset, $value);
    }

    /**
     * @inheritdoc
     *
     * @throws OutOfBoundsException
     */
    public function &offsetGet($offset)
    {
        $pair = $this->lookupKey($offset);
        if ($pair) {
            return $pair->value;
        }

        throw new OutOfBoundsException();
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset, null);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return $this->get($offset, null) !== null;
    }
}
