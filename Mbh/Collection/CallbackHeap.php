<?php namespace Mbh\Collection;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use Mbh\Collection\Interfaces\Collection as CollectionInterface;
use SplHeap;

/**
 * The CallbackHeap
 *
 * A simple class for defining a callback to use as the comparison function,
 * when building a heap. Note that this will always incur extra overhead on
 * each comparison, so if you need to define a simple heap always running the
 * same comparison, it makes more sense to define it in its own class extending
 * SplHeap. This class is appropriate when you have a set of comparisons to
 * choose from.
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */

class CallbackHeap extends SplHeap implements CollectionInterface
{
    use Traits\Collection;

    public $cb;

    public function __construct(callable $cb)
    {
        $this->cb = $cb;
    }

    public function compare($a, $b)
    {
        return call_user_func($this->cb, $a, $b);
    }
}
