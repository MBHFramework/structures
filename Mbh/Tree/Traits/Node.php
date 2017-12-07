<?php namespace Mbh\Tree\Traits;

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

trait Node
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * parent
     *
     * @var NodeInterface
     * @access private
     */
    private $parent;

    /**
     * @var NodeInterface[]
     */
    private $children = [];

    /**
     * @param mixed $value
     * @param NodeInterface[] $children
     */
    public function __construct($value = null, array $children = [])
    {
        $this->setValue($value);
        if (!empty($children)) {
            $this->setChildren($children);
        }
    }

    /**
    * {@inheritdoc}
    */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }
}
