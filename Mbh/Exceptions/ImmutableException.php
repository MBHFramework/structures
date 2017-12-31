<?php namespace Mbh\Exceptions;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use RuntimeException;

/**
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */
class ImmutableException extends RuntimeException
{
    /**
     * @param string $class
     * @param string $method
     *
     * @return static
     */
    public static function cannotModify($class, $method)
    {
        return new static(sprintf(
            'Cannot modify immutable class `%s` using array `%s` method',
            $class,
            $method
        ));
    }
}
