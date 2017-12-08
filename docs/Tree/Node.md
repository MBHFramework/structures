# The tree data structure

The `Mbh\Tree\Interfaces\Node` interface abstracts the concept of a tree node. In `Tree` a Node has essentially two things: a set of children (that implements the same `Node` interface) and a value.

On the other hand, the `Mbh\Tree\Node` gives a straight implementation for that interface.

## Creating a node

```php
<?php


use Mbh\Tree\Node;

$node = new Node('foo');
```

## Getting and setting the value of a node

Each node has a value property, that can be any php value.

```php
<?php

$node->setValue('my value');
echo $node->getValue(); //Prints 'my value'
```

## Adding one or more children

```php
<?php

$child1 = new Node('child1');
$child2 = new Node('child2');

$node
    ->addChild($child1)
    ->addChild($child2);
```

## Removing a child

```php
<?php

$node->removeChild($child1);
```

## Getting the array of all children

```php
<?php

$children = $node->getChildren();
```

## Overwriting the children set

```php
<?php

$node->setChildren([new Node('foo'), new Node('bar')]);
```

## Removing all children

```php
<?php

$node->removeAllChildren();
```

## Getting if the node is a leaf or not

A leaf is a node with no children.

```php
<?php

$node->isLeaf();
```

## Getting if the node is a child or not

A child is a node that has a parent.

```php
<?php

$node->isChild();
```

## Getting the parent of a node

Reference to the parent node is automatically managed by child-modifiers methods

```php
<?php

$root->addChild($node = new Node('child'));
$node->getParent(); // Returns $root
```

## Getting the ancestors of a node

```php
<?php

$root = (new Node('root'))
    ->addChild($child = new Node('child'))
    ->addChild($grandChild = new Node('grandchild'));

$grandchild->getAncestors(); // Returns [$root, $child]
```

### Related Methods

- `getAncestorsAndSelf` retrieves ancestors of a node with the current node included.

## Getting the root of a node

```php
<?php

$root = $node->root();
```

## Getting the neighbors of a node

```php
<?php

$root = (new Node('root'))
    ->addChild($child1 = new Node('child1'))
    ->addChild($child2 = new Node('child2'))
    ->addChild($child3 = new Node('child3'));

$child2->getNeighbors(); // Returns [$child1, $child3]
```

### Related Methods

- `getNeighborsAndSelf` retrieves neighbors of current node and the node itself.

## Getting the number of nodes in the tree

```php
<?php

$node->getSize();
```

## Getting the depth of a node

```php
<?php

$node->getDepth();
```

## Getting the height of a node

```php
<?php

$node->getHeight();
```
