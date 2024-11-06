<?php

namespace Websyspro\NestPhp\Lib\Data
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  use Websyspro\NestPhp\Lib\Data\Enums\ContentTypes;
  use Websyspro\NestPhp\Lib\Routings\Enums\HttpType;

  class DataLoad
  {
    public static array $data = [];
    
    public static function create(
    ): DataLoad {
      static::createData();
      static::createBody();
      static::createQuery();

      return new static();
    }

    private static function extractContentType(
      string $contentType
    ): string {
      [ $contentType ] = explode( ";", $contentType );
      return $contentType;
    }

    public static function contentType(
    ): string | null {
      if ( isset($_SERVER[ "CONTENT_TYPE" ]) === false ) {
        return null;
      }

      [ "CONTENT_TYPE" => $contentType,
        "CONTENT_LENGTH" => $contentLength
      ] = $_SERVER;

      return (int)$contentLength !== 0
        ? static::extractContentType( $contentType )
        : null;
    }

    public static function requestMethod(
    ): string {
      [ "REQUEST_METHOD" => $requestMethod ] = $_SERVER;
      return $requestMethod;
    }

    public static function contentLoadFile(
    ): string {
      return file_get_contents(
        "php://input"
      );
    }

    public static function contentLoadFileList(
      array $bufferArr = []
    ): array {
      $inputHandle = fopen( "php://input", "r" );
      while (( $buffer = fgets( $inputHandle, 4096 )) !== false) {
        $bufferArr[] = $buffer;
      }

      return array_slice(
        $bufferArr,
        0, sizeof(
          $bufferArr
        ) - 1
      );
    }

    private static function createData(
    ): void {
      static::$data = [
        "BODY" => [],
        "QUERY" => [],
        "PARAM" => [],
        "FILES" => [],
        "USER" => []
      ];
    }

    private static function createBody(
    ): void {
      static::requestMethod() !== HttpType::Post->value
        ? static::loadFromNotPost() : static::loadFromPost();
    }

    private static function createQuery(
    ): void {
      static::loadFromPostData(
        $_GET, "QUERY"
      );
    }

    private static function loadFromPostData(
      array $data,
      string $type
    ): void {
      Utils::mapper(
        $data,
        fn( mixed $value, string $key ) => (
          static::$data[$type][$key] = $value
        )
      );
    }

    public static function loadFromPost(
    ): void {
      if ( in_array( static::contentType(), [
        ContentTypes::ApplicationJSON->value,
        ContentTypes::MultipartFormData->value,
        ContentTypes::MultipartFormDataUrlencoded->value,
      ])){
        static::loadFromPostData(
          static::contentType() !== ContentTypes::ApplicationJSON->value
            ? $_POST : (array)json_decode( static::contentLoadFile()), "BODY"
        );
      }

      if( isset( $_FILES )) {
        Utils::mapper(
          $_FILES, fn( array $file, string $name ) => (
            static::$data["FILES"][$name] = [
              "name" => $file[ "name" ],
              "type" => $file[ "type" ],
              "size" => $file[ "size" ],
              "body" => base64_encode(
                file_get_contents(
                  $file[ "tmp_name" ]
                )
              )
            ]
          )
        );
      }
    }

    private static function extractName(
      string $value
    ): string {
      [ , $value ] = explode(
        ";",
        $value
      );
      return preg_replace(
        "/(^name=\")|(\"$)/",
        "",
        trim(
          $value
        )
      );
    }

    private static function extractFile(
      string $value
    ): string {
      [ , , $value ] = explode(
        ";",
        $value
      );
      return preg_replace(
        "/(^filename=\")|(\"$)/",
        "",
        trim(
          $value
        )
      );
    }

    private static function extractType(
      string $value
    ): string {
      return preg_replace(
        "/^Content-Type: /",
        "",
        trim(
          $value
        )
      );
    }

    private static function extractSize(
      array $value
    ): float {
      return (float)strlen(
        implode(
          "",
          array_slice(
            $value,
            3
          )
        )
      ) - 2;
    }

    private static function extractBody(
      array $value
    ): string {
      return implode(
        "",
        array_slice(
          $value,
          3
        )
      );
    }

    public static function loadFromNotPost(
      int $cursor = -1
    ): void {
      if ( static::contentType() === ContentTypes::MultipartFormData->value ) {
        foreach( static::contentLoadFileList() as $buffer ) {
          if ( preg_match(
            "/^-{28}\d+$/",
            trim(
              $buffer
            )
          ) === 1 ) {
            ++$cursor;
          }
  
          $data[
            $cursor
          ][] = $buffer;
        }
  
        Utils::mapper(
          Utils::mapper(
            $data,
            fn( array $groupBuffer ) => array_slice(
              $groupBuffer, 1
            )),
          function( array $groupBuffer ) {
            [ $content, $type ] = $groupBuffer;

            if ( empty( trim( $type ))) {
              static::$data["BODY"][
                static::extractName($content)
              ] = trim(
                implode(
                  "",
                  array_slice(
                    $groupBuffer,
                    2
                  )
                )
              );
            } else {
              static::$data["FILES"][
                static::extractName( $content )
              ] = [
                "name" => static::extractFile( $content ),
                "type" => static::extractType( $type ),
                "size" => static::extractSize( $groupBuffer ),
                "body" => base64_encode(
                  static::extractBody( $groupBuffer )
                )
              ];
            }
          }
        );
      } elseif ( static::contentType() === ContentTypes::MultipartFormDataUrlencoded->value ) {
        parse_str( static::contentLoadFile(), $data );
        static::loadFromPostData( $data, "BODY" );
      } elseif ( static::contentType() === ContentTypes::ApplicationJSON->value ) {
        Utils::mapper(
          (array)json_decode( static::contentLoadFile()),
          fn( mixed $value, mixed $key ) => (
            static::$data["BODY"][$key] = $value
          )
        );
      }
    }
  }
}
