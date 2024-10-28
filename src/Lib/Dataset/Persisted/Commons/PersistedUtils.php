<?php

namespace Websyspro\NestPhp\Lib\Dataset\Persisted\Commons
{
  use Websyspro\NestPhp\Lib\Entitys\Enums\EntityType;

  class PersistedUtils
  {
    public static function quote(
      string $value
    ): string {
      return "'{$value}'";
    }

    public static function floatEncode(
      string $value
    ): string {
      return preg_replace(
        [ "/\./i", "/\,/i" ], [ "", "." ], static::quote( $value )
      );
    }

    public static function floatDecode (
      string $value = null
    ) : string {
      if ( empty( $value ) === false ) {
        return number_format (
          $value, 2, ",", "."
        );
      }

      return (string)null;
    }

    public static function dateEncode(
      string $value
    ): string {
      return preg_replace(
        "/(\d{2})\/(\d{2})\/(\d{4})/", "$3-$2-$1", static::quote( $value )
      );
    }

    

    public static function dateDecode (
      string $value = null
    ) : string {
      return preg_replace(
        "/(\d{4})-(\d{2})-(\d{2})/", "$3/$2/$1", $value
      );
    }

    public static function varcharEncode(
      string $value
    ): string {
      return static::quote($value);
    }

    public static function timeEncode(
      string $value
    ): string {
      return static::quote($value);
    }

    public static function bigIntEncode(
      string $value
    ): string {
      return $value;
    }
    
    public static function tinyIntEncode(
      string $value
    ): string {
      return $value;
    }

    public static function tinyIntDecode(
      string $value
    ): string {
      return $value;
    }

    public static function bigIntDecode(
      string $value
    ): int {
      return (int)$value;
    }

    public static function decodeType(
      mixed $value,
      string $type
    ): string {
      return match( $type ) {
        EntityType::Date->value => static::dateDecode($value),
        EntityType::Datetime->value => static::dateDecode($value),
        EntityType::Decimal->value => static::floatDecode( $value ),
        EntityType::Tinyint->value => static::tinyIntDecode( $value ),
        EntityType::BigInt->value => static::bigIntDecode( $value ),
        
        default => $value
      };
    }

    public static function encodeType(
      string | null $value,
      string $type
    ): string | null {
      if ( $value === null ) {
        return 'null';
      }

      return match( $type ) {
        EntityType::Time->value => static::timeEncode( $value ),
        EntityType::Date->value => static::dateEncode( $value ),
        EntityType::Datetime->value => static::dateEncode( $value ),
        EntityType::Varchar->value => static::varcharEncode( $value ),
        EntityType::Decimal->value => static::floatEncode( $value ),
        EntityType::BigInt->value => static::bigIntEncode( $value ),
        EntityType::Tinyint->value => static::tinyIntEncode( $value ),
        default => static::varcharEncode($value)
      };
    }
  }
}
