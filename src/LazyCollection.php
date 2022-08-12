<?php
declare(strict_types = 1);

namespace Kolekto;

use iter;
use Iterator;
use OutOfBoundsException;
use Traversable;

class LazyCollection implements CollectionInterface {
  protected Iterator $iterator;

  public function __construct(Iterator $iterator) {
    $this->iterator = $iterator;
  }

  public function map(callable $function): CollectionInterface {
    return new static(iter\map($function, $this->iterator));
  }

  public function mapWithKeys(callable $function): CollectionInterface {
    return new static(iter\mapWithKeys($function, $this->iterator));
  }

  public function mapKeys(callable $function): CollectionInterface {
    return new static(iter\mapKeys($function, $this->iterator));
  }

  public function flatMap(callable $function): CollectionInterface {
    return new static(iter\flatMap($function, $this->iterator));
  }

  public function reindex(callable $function): CollectionInterface {
    return new static(iter\reindex($function, $this->iterator));
  }

  public function apply(callable $function): void {
    iter\apply($function, $this->iterator);
  }

  public function filter(callable $predicate): CollectionInterface {
    return new static(iter\filter($predicate, $this->iterator));
  }

  public function toPairs(): CollectionInterface {
    return new static(iter\toPairs($this->iterator));
  }

  public function fromPairs(): CollectionInterface {
    return new static(iter\fromPairs($this->iterator));
  }

  public function reductions(callable $function, mixed $startValue = null): CollectionInterface {
    return new static(iter\reductions($function, $this->iterator, $startValue));
  }

  public function merge(CollectionInterface ...$collections): CollectionInterface {
    return new static((static function (Iterator $original) use ($collections): Iterator {
      yield from $original;
      foreach ($collections as $collection) {
        yield from $collection;
      }
    })($this->iterator));
  }

  public function slice(int $start, int $length = PHP_INT_MAX): CollectionInterface {
    return new static(iter\slice($this->iterator, $start, $length));
  }

  public function take(int $num): CollectionInterface {
    return new static(iter\take($num, $this->iterator));
  }

  public function drop(int $num): CollectionInterface {
    return new static(iter\drop($num, $this->iterator));
  }

  public function takeWhile(callable $predicate): CollectionInterface {
    return new static(iter\takeWhile($predicate, $this->iterator));
  }

  public function dropWhile(callable $predicate): CollectionInterface {
    return new static(iter\dropWhile($predicate, $this->iterator));
  }

  public function keys(): CollectionInterface {
    return new static(iter\keys($this->iterator));
  }

  public function values(): CollectionInterface {
    return new static(iter\values($this->iterator));
  }

  public function flatten(int $levels = PHP_INT_MAX): CollectionInterface {
    return new static(iter\flatten($this->iterator, $levels));
  }

  public function flip(): CollectionInterface {
    return new static(iter\flip($this->iterator));
  }

  public function chunk(int $size, bool $preserveKeys = false): CollectionInterface {
    return new static(iter\chunk($this->iterator, $size, $preserveKeys));
  }

  public function chunkWithKeys(int $size): CollectionInterface {
    return new static(iter\chunkWithKeys($this->iterator, $size));
  }

  public function reduce(callable $function, mixed $startValue = null): mixed {
    return iter\reduce($function, $this->iterator, $startValue);
  }

  public function any(callable $predicate): bool {
    return iter\any($predicate, $this->iterator);
  }

  public function all(callable $predicate): bool {
    return iter\all($predicate, $this->iterator);
  }

  public function search(callable $predicate): mixed {
    return iter\search($predicate, $this->iterator);
  }

  public function count(): int {
    return iter\count($this->iterator);
  }

  public function isEmpty(): bool {
    return iter\isEmpty($this->iterator);
  }

  public function toArray(): array {
    return iter\toArray($this->iterator);
  }

  public function toArrayWithKeys(): array {
    return iter\toArrayWithKeys($this->iterator);
  }

  public function first(): mixed {
    if ($this->isEmpty()) {
      throw new OutOfBoundsException('Cannot determine first item. Collection is empty');
    }

    $item = iter\slice($this->iterator, 0, 1);

    return $item->current();
  }

  public function firstOr(mixed $default = null): mixed {
    if ($this->isEmpty()) {
      return $default;
    }

    $item = iter\slice($this->iterator, 0, 1);

    return $item->current();
  }

  public function avg(callable $function = null, float $defaultValue = 0.0): float {
    $sum = 0.0;
    $count = 0;
    foreach ($this->iterator as $key => $value) {
      if ($function !== null) {
        $value = $function($value, $key);
      }

      $sum += $item;
      $count++;
    }

    if ($count === 0) {
      return $defaultValue;
    }

    return $sum / $count;
  }

  public function max(callable $function = null): mixed {
    return $this->reduce(
      function (mixed $accumulator, mixed $value, mixed $key) use ($function): mixed {
        if ($function !== null) {
          $value = $function($value, $key);
        }

        if ($accumulator === null || $accumulator < $value) {
          return $value;
        }

        return $accumulator;
      },
      null
    );
  }

  public function min(callable $function = null): mixed {
    return $this->reduce(
      function (mixed $accumulator, mixed $value, mixed $key) use ($function): mixed {
        if ($function !== null) {
          $value = $function($value, $key);
        }

        if ($value === null) {
          return $accumulator;
        }

        if ($accumulator === null || $accumulator > $value) {
          return $value;
        }

        return $accumulator;
      },
      null
    );
  }

  public function sum(callable $function = null): float {
    $sum = 0.0;
    foreach ($this->iterator as $key => $value) {
      if ($function !== null) {
        $value = $function($value, $key);
      }

      $sum += $item;
    }

    return $sum;
  }

  /**
   * Return the current element
   */
  public function current(): mixed {
    return $this->iterator->current();
  }

  /**
   * Return the key of the current element
   */
  public function key(): mixed {
    return $this->iterator->key();
  }

  /**
   * Move forward to next element
   */
  public function next(): void {
    $this->iterator->next();
  }

  /**
   * Rewind the Iterator to the first element
   */
  public function rewind(): void {
    // $this->iterator->rewind();
  }

  /**
   * Check if current position is valid
   */
  public function valid(): bool {
    return $this->iterator->valid();
  }

  /**
   * Returns an external iterator.
   */
  public function getIterator(): Traversable {
    return $this->iterator;
  }
}
