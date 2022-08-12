<?php
declare(strict_types = 1);

namespace Kolekto;

use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;

class EagerCollection extends LazyCollection implements Countable {
  public function __construct(iterable $iterable) {
    if (is_array($iterable)) {
      parent::__construct(new ArrayIterator($iterable));

      return;
    }

    if ($iterable instanceof Iterator) {
        parent::__construct($iterable);

        return;
    }

    if ($iterable instanceof IteratorAggregate) {
        parent::__construct($iterable->getIterator());

        return;
    }

    // Traversable, but not Iterator or IteratorAggregate
    parent::__construct((static function() use ($iterable) {
        yield from $iterable;
    })());
  }

  /**
   * Rewind the Iterator to the first element
   */
  public function rewind(): void {
    $this->iterator->rewind();
  }

  public function count(): int {
    return iterator_count($this->iterator);
  }
}
