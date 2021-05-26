<?php declare(strict_types=1);

namespace Q\Collection;

use Q\FP;

final class LazyArray {
    private $data;

    public function __construct(callable $data) {
        $this->data = $data;
    }

    public static function fromArray(array $data): LazyArray {
        return new self(
            function () use ($data) {
                foreach ($data as $v) {
                    yield $v;
                }
            }
        );
    }

    public static function range($start, $end, $step = 1): LazyArray {
        return new self(
            function () use ($start, $end, $step) {
                while ($start <= $end) {
                    yield $start;
                    $start += $step;
                }
            }
        );
    }
    
    // allMatch
    public function allMatch(callable $fn): bool
    {
        foreach (call_user_func($this->data) as $val) {
            if (!$fn($val)) {
                return false;
            }
        }
        
        return true;
    }
    
    // anyMatch
    public function anyMatch(callable $fn): bool
    {
        foreach (call_user_func($this->data) as $val) {
            if ($fn($val)) {
                return true;
            }
        }
        
        return false;
    }
    
    // noneMatch
    public function noneMatch(callable $fn): bool
    {
        foreach (call_user_func($this->data) as $val) {
            if ($fn($val)) {
                return false;
            }
        }
        
        return true;
    }
    
    // count
    public function count(): int
    {
        return count($this->toArray());
    }
    
    // distinct
    public function distinct(): LazyArray
    {
        return new self(
            function () {
                $alreadySeen = [];
                foreach (call_user_func($this->data) as $val) {
                    if (is_object($val)) {
                        if (method_exists($val, '__toString')) {
                            if (!isset($alreadySeen[$val->__toString()])) {
                                $alreadySeen[$val->__toString()] = 1;
                                yield $val;
                            }
                        } else {
                            throw new \Exception("Object should implement Stringable interface");
                        }
                    } else {
                        if (!isset($alreadySeen[$val])) {
                            $alreadySeen[$val] = 1;
                            yield $val;
                        }
                    }
                }
            }
        );
    }
    
    // first
    public function first()
    {
        return FP\maybe(call_user_func($this->data)->current());
    }
    
    // peek
    public function peek(callable $fn): LazyArray
    {
        return new self(
            function () use ($fn) {
                foreach (call_user_func($this->data) as $val) {
                    call_user_func($fn, $val);
                    yield $val;
                }
            }
        );
    }
    
    // each
    public function each(callable $fn): void
    {
        foreach (call_user_func($this->data) as $val) {
            call_user_func($fn, $val);
        }
    }
    
    // take
    public function take(int $limit): LazyArray
    {
        return new self(
            function () use ($limit) {
                $i = 0;
                foreach (call_user_func($this->data) as $val) {
                    if ($i++ >= $limit) break;
                    
                    yield $val;
                }
            }
        );
    }
    
    // takeWhile
    public function takeWhile(callable $fn): LazyArray
    {
        return new self(
            function () use ($fn) {
                foreach (call_user_func($this->data) as $val) {
                    if (!$fn($val)) break;
                    
                    yield $val;
                }
            }
        );
    }
    
    // skip
    public function skip(int $limit): LazyArray
    {
        return new self(
            function () use ($limit) {
                $i = 0;
                foreach (call_user_func($this->data) as $val) {
                    if ($i++ < $limit) continue;
                    
                    yield $val;
                }
            }
        );
    }
    
    // skipWhile
    public function skipWhile(callable $fn): LazyArray
    {
        return new self(
            function () use ($fn) {
                foreach (call_user_func($this->data) as $val) {
                    if ($fn($val)) continue;
                    
                    yield $val;
                }
            }
        );
    }
    
    // map
    public function map(callable $fn): LazyArray
    {
        return new self(
            function () use ($fn) {
                foreach (call_user_func($this->data) as $val) {
                    yield $fn($val);
                }
            }
        );
    }
    
    // filter
    public function filter(callable $fn): LazyArray
    {
        return new self(
            function () use ($fn) {
                foreach (call_user_func($this->data) as $val) {
                    if ($fn($val)) {
                        yield $val;
                    }
                }
            }
        );
    }
    
    // reduce
    public function reduce(callable $fn, $initialValue = null)
    {
        $first = true;
        foreach (call_user_func($this->data) as $val) {
            if ($first && $initialValue === null) {
                $initialValue = $val;
                $first = false;
                continue;
            }
            $initialValue = $fn($initialValue, $val);
        }
        
        return $initialValue;
    }
    // toArray
    public function toArray(): array
    {
        $newArr = [];
        foreach (call_user_func($this->data) as $val) {
            $newArr[] = $val;
        }
        
        return $newArr;
    }
}
