<?php namespace Mbh\Exceptions;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

use UnderflowException;

/**
 *
 * @package structures
 * @author Ulises Jeremias Cornejo Fandos <ulisescf.24@gmail.com>
 */
class EmptyException extends UnderflowException
{
    /**
     * @param string $class
     * @param string $method
     *
     * @return static
     */
    public static function cannotAccessWhenEmpty($class, $method)
    {
        return new static(sprintf(
            '`%s` cannot be called when the `%s` structure is empty',
            $method,
            $class
        ));
    }
}
