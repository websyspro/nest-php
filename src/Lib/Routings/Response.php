<?php

namespace Websyspro\NestPhp\Lib\Routings
{
  use Websyspro\NestPhp\Lib\Routings\Enums\Headers;
  use Websyspro\NestPhp\Lib\Routings\Enums\ResponseCode;

  class Response
  {
    private function header(
    ): void {
      header( Headers::accessControlAllowOrigin->value );
      header( Headers::accessControlAllowHeaders->value );
      header( Headers::accessControlAllowMethods->value );
      header( Headers::applicationJSON->value);
    }

    private function responseCode(
      ResponseCode $responseCode = ResponseCode::ok
    ): void {
      http_response_code(
        $responseCode->value
      );
    }

    public function send(
      mixed $content,
       bool $success = true,
      ResponseCode $responseCode = ResponseCode::ok
    ): void {
      $this->header();
      $this->responseCode(
        $responseCode
      );

      exit( json_encode(
        [
          "success" => $success,
          "content" => $content
        ],
        JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION
      ));
    }
  }
}
