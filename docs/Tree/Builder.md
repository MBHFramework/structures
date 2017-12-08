# The Builder

The builder provides a convenient way to build trees. It is provided by the `Mbh\Tree\Builder` class, but you can implement your own builder making an implementation of the `Mbh\Tree\Interfaces\Builder` class.

## Example

Let's see how to build the following tree, where the nodes label are represents nodes values:

```
       A
      / \
     B   C
        /|\
       D E F
      /|
     G H
```

And here is the code:

```php
<?php

$builder = new \Mbh\Tree\Builder;

$builder
    ->value('A')
    ->leaf('B')
    ->tree('C')
        ->tree('D')
            ->leaf('G')
            ->leaf('H')
            ->end()
        ->leaf('E')
        ->leaf('F')
        ->end()
;

$nodeA = $builder->getNode();
```

The example should be self-explanatory, but here you are a brief description of the methods used above.

## Builder::value($value)

Set the value of the current node to `$value`

## Builder::leaf($value)

Add to the current node a new child whose value is `$value`.

## Builder::tree($value)

Add to the current node a new child whose value is `$value`, and set the new node as the builder current node.

## Builder::end()

Returns to the context the builder was before the call to `tree`method, i.e. make the builder go one level up.

## Builder::getNode()

Returns the current node.
