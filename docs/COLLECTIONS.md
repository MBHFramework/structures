# Immutable Collections

Immutable collections, with filter, map, join, sort, slice, and other methods. Well-suited for functional programming and memory-intensive applications. Runs especially fast in PHP 7.

## Basic Usage

### Quickly load from a simple array

```php
<?php

use Mbh\Collection\ImmutableArray;
$polite = ImmutableArray::fromArray(['set', 'once', 'don\'t', 'mutate']);
echo $polite->join(' ');
// => "set once don't mutate"
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

// => <article>
// =>   <h3>A Wonderful List</h3>
// =>   <ul>
// =>     <li>SET</li><li>ONCE</li><li>DON'T</li><li>MUTATE</li>
// =>   </ul>
// => </article>
```

### Sort with a callback

```php
<?php

echo 'Os in front: ' .
    $yelling
        ->sort(function ($a, $b) { return strcmp($a, $b); })
        ->join(' ');

// => "Os in front: DON'T MUTATE ONCE SET"
```
