# Tree

In Tree you can find a basic but flexible tree data structure for php together with and an handful Builder class, that enables you to build tree in a fluent way.

## Node

The `Mbh\Tree\Interfaces\Node` interface abstracts the concept of a tree node. In `Tree` a Node has essentially two things: a set of children (that implements the same `Node` interface) and a value.

On the other hand, the `Mbh\Tree\Node` gives a straight implementation for that interface.

You can read the following [file](./docs/Tree/Node.md) to learn how the structure works.

## Builder

The builder provides a convenient way to build trees. It is provided by the `Mbh\Tree\Builder` class, but you can implement your own builder making an implementation of the `Mbh\Tree\Interfaces\Builder` class.

You can read the following [file](./docs/Tree/Builder.md) to learn how the structure works.
