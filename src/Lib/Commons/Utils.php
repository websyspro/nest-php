<?php

namespace Websyspro\NestPhp\Lib\Commons
{
  use ReflectionFunction;
    use Websyspro\NestPhp\Lib\Data\DataLoad;

  class Utils extends UtilsArrays
  {
    public static function betweenRelatives(
      string $value
    ): string {
      return "({$value})";
    }

    public static function isString(
      string $value
    ): bool {
      return is_string(
        $value
      );
    }
    
    public static function quoteStr(
      string $str
    ): string {
      return "`{$str}`";
    }

    public static function entityName(
      string | object $entity
    ): string {
      return preg_replace( "/Entity$/", "", static::EndArray(
        explode( "\\", $entity )
      ));
    }

    public static function contextName(
      string | object $entity
    ): string {
      return mb_strtolower(
        preg_replace(
          "/Context$/",
          "",
          static::EndArray(
            explode(
              "\\",
              $entity
            )
          )
        )
      );
    }

    public static function now(
    ): string {
      return date("Y-m-d H:i:s");
    }

    public static function getUserId(
    ): int {
      return (int)DataLoad::$data[
        "USER"
      ][ "id" ];
    }

    public static function getRandKey(
    ): string {
      return substr(
        str_shuffle(
          static::join(
            [
              "qazwsxedcrfvtgbyhnujmikolp",
              "QAZWSXEDCRFVTGBYHNUJMIKOLP",
              "01234567890"
            ]
          )
        ), 0, 32
      );
    }
    
    public static function guid(
    ): string {
      $dataGuid = random_bytes( 16 );
      assert( strlen( $dataGuid ) == 16);
    
      $dataGuid[ 6 ] = chr( ord( $dataGuid[6] ) & 0x0f | 0x40 );
      $dataGuid[ 8 ] = chr( ord( $dataGuid[8] ) & 0x3f | 0x80 );

      return mb_strtoupper(
          vsprintf(
          "%s%s-%s-%s-%s-%s%s%s",
          str_split(
            bin2hex($dataGuid),
            4
          )
        )
      );
    }
  }
}
