<?php

namespace Websyspro\NestPhp\Lib\Commons
{
  use ReflectionFunction;

  class UtilsArrays
  {
    public static function shitArray(
      array $arr = []
    ): mixed {
      return array_shift($arr);
    }

    public static function endArray(
      array $arr = []
    ): mixed {
      $arr = array_reverse($arr);
      return array_shift($arr);
    }

    public static function destructArrayKeys(
      array $columnArr = []
    ): array {
      return [
        static::ShitArray(
          array_keys( $columnArr )
        ),
        $columnArr[
          static::ShitArray(
            array_keys( $columnArr )
          )
        ]
      ];
    }
    
    public static function getNumberOfArgs(
      callable $callable
    ): int {
      return (
        new ReflectionFunction( $callable)
      )->getNumberOfParameters();
    }

    public static function join(
      array $joinArr,
      string $joinSeparetor = ","
    ): string {
      return implode(
        $joinSeparetor,
        $joinArr
      );
    }

    public static function isValidArray(
      array $isValidArr = []
    ): bool {
      return is_array( $isValidArr ) && empty( $isValidArr );
    }

    public static function isEmptyArray(
      array $value = []
    ): bool {
      return is_array( $value ) && empty( $value ) === true;
    }

    public static function mapper(
      array $mapperArr,
      callable $mapperEvt,
      array $mapperArrResult = []
    ): array {
      foreach( $mapperArr as $key => $val ){
        $mapperArrResult[$key] = static::getNumberOfArgs( $mapperEvt ) === 2
          ? $mapperEvt( $val, $key ) : $mapperEvt( $val );
      }

      return $mapperArrResult;
    }

    public static function mapperKey(
      array $mapperArr,
      callable $mapperEvt,
      array $mapperArrResult = []
    ): array {
      foreach( $mapperArr as $key => $val ){
        $mapperArrResult[$key] = $mapperEvt( $key );
      }

      return $mapperArrResult;
    }

    public static function mapperFirst(
      array $mapperArr,
      callable $mapperEvt
    ): array {
      return static::shitArray(
        static::mapper(
          $mapperArr,
          $mapperEvt
        )
      );
    }

    public static function filter(
      array $mapperArr,
      callable $mapperEvt,
      array $mapperArrResult = []
    ): array {
      foreach( $mapperArr as $key => $val ){
        if ( static::getNumberOfArgs( $mapperEvt ) === 2 ) {
          if ( $mapperEvt( $val, $key ) === true ) {
            $mapperArrResult[ $key ] = $val;
          }
        } else {
          if( $mapperEvt( $val ) === true) {
            $mapperArrResult[ $key ] = $val;
          }
        }
      }

      return $mapperArrResult;
    }

    public static function filterKey(
      array $mapperArr,
      callable $mapperEvt,
      array $mapperArrResult = []
    ): array {
      foreach( $mapperArr as $key => $val ){
        if( $mapperEvt( $key ) === true) {
          $mapperArrResult[ $key ] = $val;
        }
      }

      return $mapperArrResult;
    }

    public static function reduce(
      array $reduceArr,
      mixed $reduceInitial,
      callable $reduceCallable
    ): mixed {
      return array_reduce(
        $reduceArr,
        $reduceCallable,
        $reduceInitial
      );
    }

    public static function arrayMerge(
      array $arr1 = [],
      array $arr2 = []
    ): array {
      return array_merge(
        $arr1, $arr2
      );
    }

    public static function arrayFlip(
      array $arrayFlip = []
    ): array {
      return array_flip(
        $arrayFlip
      );
    }
  }
}
