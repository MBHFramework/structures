# Collections

## Mutable Collections

### Deque

### Fixed Array

Fixed collections, with filter, map, join, sort, slice, and other methods. Well-suited for functional programming and memory-intensive applications. Runs especially fast in PHP 7.

You can read the following [file](./Collections/FixedArray.md) to learn how the structure works.

### Map

### Queue

### Set

### Stack

## Immutable Collections

### ImmutableArray

### ImmutableSet

### ImmutableMap

## Why didn't we just use Spl Data Structures directly?

We can answer this question by comparing some of the structures offered by Spl Data Structures and one of those that can be found in this library. For example, I could compare SplFixedArray with `Mbh\Collection\FixedArray`. The SplFixedArray is very nicely implemented at the low-level, but is often somewhat painful to actually use. Its memory savings vs standard arrays (which are really just variable-sized hashmaps -- the most mutable datastructure I can think of) can be enormous, though perhaps not quite as big a savings as it will be once PHP7 gets here. By composing an object with the SplFixedArray, we can have a class which solves the usability issues, while maintaining excellent performance.

### Static-Factory Methods

The SPL datastructures are all very focused on an inheritance-approach, but I found the compositional approach taken in hacklang collections to be far nicer to work with. Indeed, the collections classes in hack are all `final`, implying that you must build your own datastructures composed of them, so I took the same approach with SPL. The big thing you miss out on with inheritance is the `fromArray` method, which is implemented in C and quite fast, however:

```php
<?php

class FooFixed extends SplFixedArray {}

$foo = FooFixed::fromArray([1, 2, 3]);

echo get_class($foo);
// "SplFixedArray"
```

So you can see that while the static class method `fromArray()` was called from a FooFixed class, our `$foo` is not a `FooFixed` at all, but an `SplFixedArray`.

`Mbh\Collection\FixedArray`, however, uses a compositional approach so we can statically bind the factory methods:

```php
<?php

use Mbh\Collection\FixedArray;

class FooFixed extends FixedArray {}

$foo = FooFixed::fromArray([1, 2, 3]);

echo get_class($foo);
// "FooFixed"
```

Now that dependency injection, and type-hinting in general, are all the rage, it's more important than ever that our datastructures can be built as objects for the class we want. It's doubly important, because implementing a similar `fromArray()` in PHP is many times slower than the C-optimized `fromArray()` we use here.

### De-facto standard array functions

The good ol' PHP library has a pile of often useful, generally well-performing, but crufty array functions with inconsistent interfaces (e.g. `array_map($callback, $array)` vs `array_walk($array, $callback)`). Dealing with these can be considered one of PHP's quirky little charms. The real problem is, these functions all have one thing in common: your object _must_ be an array. Not arraylike, not ArrayAccessible, not Iterable, not Traversable, etc., but an array. By building in functions so common in JavaScript and elsewhere, e.g. `map()`, `filter()`, and `join()`, one can easily build new arrays by passing a callback to the old one.

```php
<?php

use Mbh\Collection\FixedArray;

$foo = FixedArray::fromArray([1, 2, 3, 4, 5]);

echo $foo->map(function($el) { return $el * 2; })->join(', ');

// => "2, 4, 6, 8, 10"
```

### Serialize as JSON

More and more, PHP is being used less for bloated, view-logic heavy applications, and more as a thin data layer that exists to provide business logic against a datasource, and be consumed by a client side or remote application. I've found most of what I write nowadays simply renders to JSON, which I'll load in a React.js or ember application in the browser. In the interest of being nice to JavaScript developers, it's important to send arrays as arrays, not "arraylike" objects which need to have a bunch of `Object.keys` magic used on them.e.g.

```php
<?php

$foo = SplFixedArray::fromArray([1, 2, 3]);

echo json_encode($foo);
// => {"0":1,"1":2,"2":3}
```

The internal logic makese sense to a PHP dev here -- you're encoding properties, after all, but this format is undesirable when working in JS. Objects in js are unordered, so you need to loop through a separate counter, and lookup each string property-name by casting the counter back to string, doing a property lookup, and ending the loop once you've reached the length of the object keys. It's a silly we often have to endure, when we'd much rather get back an array in the first place. e.g.

```php
<?php

use Mbh\Collection\FixedArray;

$foo = FixedArray::fromArray([1, 2, 3]);

echo json_encode($foo);
// => [1, 2, 3]
```
