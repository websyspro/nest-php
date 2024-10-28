<?php

namespace Websyspro\NestPhp\Lib\Routings
{
  use Websyspro\NestPhp\Lib\Routings\Exceptions\BadRequest;
  use Websyspro\NestPhp\Lib\Routings\Exceptions\NotFound;
    use Websyspro\NestPhp\Lib\Routings\Exceptions\UnAuthorized;

  class Error
  {
    public static function badRequest( $message ): void {
      throw new BadRequest( $message );
    }

    public static function notFound( $message ): void {
      throw new NotFound( $message );
    }

    public static function unAuthorized( $message ): void {
      throw new UnAuthorized( $message );
    }
  }
}
