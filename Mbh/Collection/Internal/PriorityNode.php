<?php namespace Mbh\Collection\Internal;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

/**
 * @internal
 */
final class PriorityNode
{
    /**
     * @var mixed
     */
    public $value;

    /**
     * @var int
     */
    public $priority;

    /**
     * @var int
     */
    public $stamp;

    /**
     * @param mixed $value
     * @param int   $priority
     * @param int   $stamp
     */
    public function __construct($value, int $priority, int $stamp)
    {
        $this->value    = $value;
        $this->priority = $priority;
        $this->stamp    = $stamp;
    }
}
