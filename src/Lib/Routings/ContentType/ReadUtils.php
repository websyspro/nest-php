<?php

namespace Websyspro\NestPhp\Lib\Routings\ContentType
{
  use Websyspro\NestPhp\Lib\Commons\Utils;

  class ReadUtils
  {
    public function contentMethod(): string {
      [ "REQUEST_METHOD" => $requestMethod ] = $_SERVER;
      return $requestMethod;
    }

    public function contentType(
    ): string{
      return preg_replace(
        "/;.*$/",
        "",
        $_SERVER[ "CONTENT_TYPE" ]
      );
    }

    public function contentFile(
    ): mixed {
      return fopen(
        "php://input",
        "r"
      );
    }

    public function getValueFromName(
      string $value
    ): string {
      return preg_replace(
        "/(^name=\")|(\"$)/",
        "",
        trim($value)
      );
    }

    public function contentLoadDataFile(
      mixed $resource,
      int $resourceSize = 4096,
      int $cursor = -1
    ): array {
      while (( $buffer = fgets(
        $resource,
        $resourceSize
      )) !== false ) {
        if ( preg_match(
          "/^-{28}\d+$/",
          trim(
            $buffer
          )
        ) === 1 ) {
          ++$cursor;
        }

        if ( preg_match(
          "/^-{28}\d+-{2}$/",
          trim(
            $buffer
          )
        ) === 0 ) {
          $buffers[
            $cursor
          ][] = $buffer;
        }
      }

      $buffers = Utils::mapper(
        $buffers,
        fn( array $buffersArr ) => (
          array_slice(
            $buffersArr,
            1
          )
        )
      );
      
      $buffers = Utils::mapper(
        $buffers,
        function( array $buffersArr ) {
          [ $contentDisposition,
            $contentType ] = $buffersArr;

          if ( empty( trim( $contentType )) ) {
            [ , $name ] = explode(
              ";",
              $contentDisposition
            );

            return [
              $this->getValueFromName($name) => trim(
                implode(
                  "",
                  array_slice(
                    $buffersArr,
                    2
                  )
                )
              )
            ];
          } else {
            [ , $name ] = explode(
              ";",
              $contentDisposition
            );

            return [
              $this->getValueFromName($name) => implode(
                "",
                array_slice(
                  $buffersArr,
                  2
                )
              )
            ];
          }
        }
      );

      return $buffers;
    }
  }
}
