<?php
declare(strict_types = 1);

namespace Kolekto;

use IteratorAggregate;

interface CollectionInterface extends IteratorAggregate {
  /**
   * Applies a mapping function to all collection values.
   *
   * The function is passed the current iterator value and should return a
   * modified iterator value. The key is left as-is and not passed to the mapping
   * function.
   *
   * @param callable $function function (mixed $value): mixed
   */
  public function map(callable $function): CollectionInterface;
  /**
   * Applies a mapping function to all collection values, passing both the key and the value into
   * the callback.
   *
   * The function is passed the current iterator value and key and should return a
   * modified iterator value. The key is left as-is but passed to the mapping
   * function as the second parameter.
   *
   * @param callable $function function (mixed $value, mixed $key): mixed
   */
  public function mapWithKeys(callable $function): CollectionInterface;
  /**
   * Applies a mapping function to all collection keys.
   *
   * The function is passed the current iterator key and should return a
   * modified iterator key. The value is left as-is and not passed to the mapping
   * function.
   *
   * @param callable $function function (mixed $key): mixed
   */
  public function mapKeys(callable $function): CollectionInterface;
  /**
   * Applies a function to each collection value and flattens the result.
   *
   * The function is passed the current iterator value and should return an
   * iterator of new values. The result will be a concatenation of the iterators
   * returned by the mapping function.
   *
   * @param callable $function function (mixed $value): Iterator
   */
  public function flatMap(callable $function): CollectionInterface;
  /**
   * Reindexes an array by applying a function to all collection values and
   * using the returned value as the new key/index.
   *
   * The function is passed the current iterator value and should return a new
   * key for that element. The value is left as-is. The original key is not passed
   * to the mapping function.
   *
   * @param callable $function function (mixed $value): mixed
   */
  public function reindex(callable $function): CollectionInterface;
  /**
   * Applies a function to all collection values.
   *
   * The function is passed the current iterator value. The reason why apply
   * exists additionally to map is that map is lazy, whereas apply is not (i.e.
   * you do not need to consume a resulting iterator for the function calls to
   * actually happen.)
   *
   * @param callable $function function (mixed $value): void
   */
  public function apply(callable $function): void;
  /**
   * Filters all collection values using a predicate.
   *
   * The predicate is passed the iterator value, which is only retained if the
   * predicate returns a truthy value. The key is not passed to the predicate and
   * left as-is.
   *
   * @param callable $predicate function (mixed $value): bool
   */
  public function filter(callable $predicate): CollectionInterface;
  /**
   * Converts a "key => value" into a "[key, value]" pair.
   */
  public function toPairs(): CollectionInterface;
  /**
   * Converts a "[key, value]" into a "key => value" pair.
   */
  public function fromPairs(): CollectionInterface;
  /**
   * Intermediate values of reducing the collection using a function.
   *
   * The reduction function is passed an accumulator value and the current
   * iterator value and returns a new accumulator. The accumulator is initialized
   * to $startValue.
   *
   * Reductions yield each accumulator along the way.
   *
   * @param callable $function function (mixed $accumulator, mixed $value, mixed $key): mixed
   */
  public function reductions(callable $function, mixed $startValue = null): CollectionInterface;
  /**
   * Merge the collections.
   *
   * The resulting collection will contain the values of the first collection, then the second, and
   * so on.
   */
  public function merge(CollectionInterface ...$collections): CollectionInterface;
  /**
   * Takes a slice from collection items.
   */
  public function slice(int $start, int $length = PHP_INT_MAX): CollectionInterface;
  /**
   * Takes the first n items from collection.
   */
  public function take(int $num): CollectionInterface;
  /**
   * Drops the first n items from collection.
   */
  public function drop(int $num): CollectionInterface;
  /**
   * Takes items from collection until the predicate fails for the first time.
   *
   * This means that all elements before (and excluding) the first element on
   * which the predicate fails will be returned.
   *
   * @param callable $predicate function (mixed $value): bool
   */
  public function takeWhile(callable $predicate): CollectionInterface;
  /**
   * Drops items from collection until the predicate fails for the first time.
   *
   * This means that all elements after (and including) the first element on
   * which the predicate fails will be returned.
   *
   * @param callable $predicate function (mixed $value): bool
   */
  public function dropWhile(callable $predicate): CollectionInterface;
  /**
   * Returns the keys of collection items.
   */
  public function keys(): CollectionInterface;
  /**
   * Returns the values of collection items, making the keys continuously indexed.
   */
  public function values(): CollectionInterface;
  /**
   * Takes an iterable containing any amount of nested iterables and returns
   * a flat iterable with just the values.
   *
   * The $level argument allows to limit flattening to a certain number of levels.
   */
  public function flatten(int $levels = PHP_INT_MAX): CollectionInterface;
  /**
   * Flips the keys and values of an iterable.
   */
  public function flip(): CollectionInterface;
  /**
   * Chunks an iterable into arrays of the specified size.
   *
   * Each chunk is an array (non-lazy), but the chunks are yielded lazily.
   * By default keys are not preserved.
   */
  public function chunk(int $size, bool $preserveKeys = false): CollectionInterface;
  /**
   * The same as chunk(), but preserving keys.
   */
  public function chunkWithKeys(int $size): CollectionInterface;
  /**
   * Reduce the collection using a function.
   *
   * The reduction function is passed an accumulator value and the current
   * iterator value and returns a new accumulator. The accumulator is initialized
   * to $startValue.
   *
   * @param callable $function function (mixed $accumulator, mixed $value, mixed $key): mixed
   */
  public function reduce(callable $function, mixed $startValue = null): mixed;
  /**
   * Returns true if there is a value in the collection that satisfies the
   * predicate.
   *
   * This function is short-circuiting, i.e. if the predicate matches for any one
   * element the remaining elements will not be considered anymore.
   *
   * @param callable $predicate function (mixed $value): bool
   */
  public function any(callable $predicate): bool;
  /**
   * Returns true if all values in the collection satisfy the predicate.
   *
   * This function is short-circuiting, i.e. if the predicate fails for one
   * element the remaining elements will not be considered anymore.
   *
   * @param callable $predicate function (mixed $value): bool
   */
  public function all(callable $predicate): bool;
  /**
   * Searches the collection until a predicate returns true, then returns
   * the value of the matching element.
   *
   * @param callable $predicate function (mixed $value): bool
   */
  public function search(callable $predicate): mixed;
  /**
   * Returns the number of elements the collection contains.
   *
   * This function is not recursive, it counts only the number of elements in the
   * iterable itself, not its children.
   *
   * If the iterable implements Countable its count() method will be used.
   */
  public function count(): int;
  /**
   * Determines whether the collection is empty.
   *
   * If the iterable implements Countable, its count() method will be used.
   * Calling isEmpty() does not drain iterators, as only the valid() method will
   * be called.
   */
  public function isEmpty(): bool;
  /**
   * Converts the collection into an array, without preserving keys.
   *
   * Not preserving the keys is useful, because iterators do not necessarily have
   * unique keys and/or the key type is not supported by arrays.
   */
  public function toArray(): array;
  /**
   * Converts the collection into an array and preserves its keys.
   *
   * If the keys are not unique, newer keys will overwrite older keys. If a key
   * is not a string or an integer, the usual array key casting rules (and
   * associated notices/warnings) apply.
   */
  public function toArrayWithKeys(): array;

  public function first(): mixed;

  public function firstOr(mixed $default = null): mixed;

  /**
   * Returns the average value of all collection values.
   *
   * The function is passed the current iterator value and key and should return a
   * modified iterator value. The key is left as-is but passed to the mapping
   * function as the second parameter.
   *
   * @param callable $function function (mixed $value, mixed $key): float
   */
  public function avg(callable $function = null, float $defaultValue = 0.0): float;
  /**
   * Returns the max value among all collection values.
   *
   * The function is passed the current iterator value and key and should return a
   * modified iterator value. The key is left as-is but passed to the mapping
   * function as the second parameter.
   *
   * @param callable $function function (mixed $value, mixed $key): mixed
   */
  public function max(callable $function = null): mixed;
  /**
   * Returns the min value among all collection values.
   *
   * The function is passed the current iterator value and key and should return a
   * modified iterator value. The key is left as-is but passed to the mapping
   * function as the second parameter.
   *
   * @param callable $function function (mixed $value, mixed $key): mixed
   */
  public function min(callable $function = null): mixed;
  /**
   * Returns the sum value of all collection values.
   *
   * The function is passed the current iterator value and key and should return a
   * modified iterator value. The key is left as-is but passed to the mapping
   * function as the second parameter.
   *
   * @param callable $function function (mixed $value, mixed $key): float
   */
  public function sum(callable $function = null): float;
}
