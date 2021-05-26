<?php declare(strict_types=1);

namespace Q\Collection\tests\units;

use atoum;

class LazyArray extends atoum
{
    public function testFromArray()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->given($arr = [1, 2, 3])
            ->then
              ->object($lazyArray = $class::fromArray($arr))
              ->array($lazyArray->toArray())
              ->isEqualTo([1, 2, 3]);
    }

    public function testRange()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::range(1, 3))
                  ->array($lazyArray->toArray())
                  ->isEqualTo([1, 2, 3]);
    }

    public function testRangeWithCustomStep()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::range(1, 5, 2))
                  ->array($lazyArray->toArray())
                  ->isEqualTo([1, 3, 5]);
    }

    public function testAllMatch()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::range(1, 3))
                  ->boolean($lazyArray->allMatch(fn ($x) => $x < 5))
                  ->isTrue();

        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::range(1, 3))
                  ->boolean($lazyArray->allMatch(fn ($x) => $x < 2))
                  ->isFalse();
    }

    public function testAnyMatch()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::range(1, 3))
                  ->boolean($lazyArray->anyMatch(fn ($x) => $x < 5))
                  ->isTrue();

        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::range(1, 3))
                  ->boolean($lazyArray->anyMatch(fn ($x) => $x > 3))
                  ->isFalse();
    }

    public function testNoneMatch()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::range(1, 3))
                  ->boolean($lazyArray->noneMatch(fn ($x) => $x < 5))
                  ->isFalse();

        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::range(1, 3))
                  ->boolean($lazyArray->noneMatch(fn ($x) => $x > 3))
                  ->isTrue();
    }

    public function testCount()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::range(1, 3))
                  ->integer($lazyArray->count())
                  ->isEqualTo(3);
    }

    public function testDistinct()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 2, 3]))
                  ->object($distinctArray = $lazyArray->distinct())
                  ->array($distinctArray->toArray())
                  ->isEqualTo([1, 2, 3]);
    }

    public function testDistinctStringable()
    {
        $testClass = new class {
            private $a = 1;

            public function __toString() {
                return (string)$this->a;
            }
        };

        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([
                      $testClass,
                      $testClass,
                      $testClass
                  ]))
                  ->object($distinctArray = $lazyArray->distinct())
                  ->array($distinctArray->toArray())
                  ->hasSize(1);
    }

    public function testFirst()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3]))
                  ->object($first = $lazyArray->first())
                  ->isInstanceOf('\Q\FP\Monad\Maybe\Just');

        $this
            ->given($first)
            ->then
            ->integer($first->fromJust())
            ->isEqualTo(1);

        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([]))
                  ->object($lazyArray->first())
                  ->isInstanceOf('\Q\FP\Monad\Maybe\Nothing');
    }

    public function testPeek()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3])->peek(fn ($x) => print($x)))
                  ->array($lazyArray->toArray())
                  ->hasSize(3)
                  ->output(fn () => $lazyArray->toArray())
                  ->isEqualTo('123');
    }

    public function testEach()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3]))
                  ->output(fn () => $lazyArray->each(fn ($x) => print($x)))
                  ->isEqualTo('123');
    }

    public function testTake()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3])->take(2))
                  ->array($lazyArray->toArray())
                  ->isEqualTo([1, 2]);

        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3])->take(0))
                  ->array($lazyArray->toArray())
                  ->isEmpty();

        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3])->take(10))
                  ->array($lazyArray->toArray())
                  ->isEqualTo([1, 2, 3]);
    }

    public function testTakeWhile()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3])->takeWhile(fn ($x) => $x <= 2))
                  ->array($lazyArray->toArray())
                  ->isEqualTo([1, 2]);

        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3])->takeWhile(fn ($x) => $x < 0))
                  ->array($lazyArray->toArray())
                  ->isEmpty();

        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3])->takeWhile(fn ($x) => $x < 10))
                  ->array($lazyArray->toArray())
                  ->isEqualTo([1, 2, 3]);
    }

    public function testSkip()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3])->skip(2))
                  ->array($lazyArray->toArray())
                  ->isEqualTo([3]);

        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3])->skip(0))
                  ->array($lazyArray->toArray())
                  ->isEqualTo([1, 2, 3]);

        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3])->skip(10))
                  ->array($lazyArray->toArray())
                  ->isEmpty();
    }

    public function testSkipWhile()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3])->skipWhile(fn ($x) => $x <= 2))
                  ->array($lazyArray->toArray())
                  ->isEqualTo([3]);

        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3])->skipWhile(fn ($x) => $x < 0))
                  ->array($lazyArray->toArray())
                  ->isEqualTo([1, 2, 3]);

        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3])->skipWhile(fn ($x) => $x < 10))
                  ->array($lazyArray->toArray())
                  ->isEmpty();
    }

    public function testMap()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3])->map(fn ($x) => $x * 2))
                  ->array($lazyArray->toArray())
                  ->isEqualTo([2, 4, 6]);
    }

    public function testFilter()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->object($lazyArray = $class::fromArray([1, 2, 3])->filter(fn ($x) => $x % 2 == 0))
                  ->array($lazyArray->toArray())
                  ->isEqualTo([2]);
    }

    public function testReduce()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->integer($class::fromArray([1, 2, 3])->reduce(fn ($res, $x) => $res * $x))
                  ->isEqualTo(6);

        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                  ->integer($class::fromArray([1, 2, 3])->reduce(fn ($res, $x) => $res * $x, 2))
                  ->isEqualTo(12);
    }
}
