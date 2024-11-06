<?php

namespace Websyspro\NestPhp\Lib\Routings\Decorations\Middlewares
{
  use Attribute;
  use Websyspro\NestPhp\Lib\Commons\JWT;
    use Websyspro\NestPhp\Lib\Data\DataLoad;
    use Websyspro\NestPhp\Lib\Routings\Enums\DecorationType;
  use Websyspro\NestPhp\Lib\Routings\Error;

  #[Attribute( Attribute::TARGET_CLASS )]
  class Authenticate
  {
    public DecorationType $decorationType = DecorationType::Middleware;

    public function __construct(
      private string | null $jwtKey = null
    ){}

    public function execute(
    ): void {
      if ( isset( $_SERVER[ "HTTP_AUTHORIZATION" ]) === false ) {
        Error::unAuthorized(
          "Authorization token not provided."
        );
      }

      [ "HTTP_AUTHORIZATION" => $httpAuthorization ] = $_SERVER;
      [ $authorizationType, $authorizationJWT ] = explode(
        " ", $httpAuthorization
      );

      if ( $authorizationType !== "Bearer" ) {
        Error::unAuthorized(
          "invalid authorization type."
        );
      }

      if ( empty( $authorizationJWT )) {
        Error::unAuthorized(
          "Authorization token not provided."
        );
      }

      $jwtDecode = JWT::decode(
        $authorizationJWT,
        $this->jwtKey ?? APP_JWT_KEY,
        [ "HS256" ]
      );

      if( (int)$jwtDecode->exp < (int)time()) {
        Error::unAuthorized(
          "Expired token"
        );
      }

      DataLoad::$data[ "USER" ] = [
        "id" => $jwtDecode->data->id
      ];
    }
  }
}
