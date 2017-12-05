# Immutable Collections

Immutable collections, with filter, map, join, sort, slice, and other methods. Well-suited for functional programming and memory-intensive applications. Runs especially fast in PHP 7.

## Basic Usage

### Quickly load from a simple array

```php
<?php

use Mbh\Collection\ImmutableArray;
$polite = ImmutableArray::fromArray(['set', 'once', 'don\'t', 'mutate']);
echo $polite->join(' ');
// "set once don't mutate"
```

### Map with a callback

```php
<?php

$yelling = $polite->map(function($word) {
  return strtoupper($word);
});

echo <<<EOT
<article>
  <h3>A Wonderful List</h3>
  <ul>
    {$yelling->join('<li>', '</li>')}
  </ul>
</article>
EOT;

// <article>
//   <h3>A Wonderful List</h3>
//   <ul>
//     <li>SET</li><li>ONCE</li><li>DON'T</li><li>MUTATE</li>
//   </ul>
// </article>
```

### Sorted with a callback

```php
<?php

echo 'Ordered ascending: ' .
    $yelling
        ->sorted(function ($a, $b) { return strcmp($a, $b); })
        ->join(' ');

// "Ordered ascending: DON'T MUTATE ONCE SET"
```

### Slice

```php
<?php

echo 'First 2 words only: ' . $polite->slice(0, 2)->join(' ');
// "set once"
```

## More of the same

Until recently we saw examples of how to use certain features in small arrays. Now we could go to another level, and apply other functions to slightly larger arrays.

### Load big objects

```php
<?php

use Mbh\Collection\ImmutableArray;

// Big memory footprint: $fruits is 30MB on PHP5.6
$fruits = array_merge(array_fill(0, 1000000, 'tomato'), array_fill(0, 1000000, 'apple'));

// Small memory footprint: only 12MB
$fruitsImm = ImmutableArray::fromArray($fruits);

// Especially big savings for slices -- array_slice() gives a 31MB object
$range = range(0, 50000);
$sliceArray = array_slice($range, 0, 30000);

// But this is a 192 bytes iterator!
$immSlice = ImmutableArray::fromArray($range)->slice(0, 30000);
```

### Filter

```php
<?php

// Yes, we have no apples
$noApples = $fruitsImm->filter(function($fruit) { return $fruit !== 'apple'; });
```

## Back to small arrays

### Concat

```php
<?php

$ia = ImmutableArray::fromArray([1, 2, 3, 4]);
$ib = ImmutableArray::fromArray([5, 6, 7, 8]);

// Like in slice() method, it's just a little iterator in-memory
$ic = $ia->concat($ib);
// [1, 2, 3, 4, 5, 6, 7, 8]
```

### Reduce

```php
<?php

$fruits = ImmutableArray::fromArray(['peach', 'plum', 'orange']);

$fruits->reduce(function($last, $cur, $i) {
  return $last . '{"' . $i . '":' . $cur . '"},';
}, 'My Fruits: ');

// My Fruits: {"0":"peach"},{"1":"plum"},{"2":"orange"},
```

### Find

```php
<?php

$fruits = ImmutableArray::fromArray(['peach', 'apple', 'plum', 'banana', 'orange']);

$fruitILike = $fruits->find(function ($fruit) {
  return $fruit === 'banana';
});

// 'banana'
```

### Array accessible

```php
<?php

echo $fruits[1];
// 'apple'
```

### Countable

```php
<?php

count($fruits);
// 5
```

### Iterable

```php
<?php

foreach ($fruits as $fruit) {
    $fruitCart->sell($fruit);
}
```

### Load from any Traversable object

```php
<?php

$vegetables = ImmutableArray::fromItems($vegetableIterator);
```
