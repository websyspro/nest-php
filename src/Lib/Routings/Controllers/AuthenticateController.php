<?php

namespace Websyspro\NestPhp\Lib\Routings\Controllers
{
  use Websyspro\NestPhp\Lib\Commons\JWT;
  use Websyspro\NestPhp\Lib\Routings\Decorations\Body;
  use Websyspro\NestPhp\Lib\Routings\Decorations\HttpPost;
  use Websyspro\NestPhp\Lib\Routings\Decorations\Middlewares\Controller;

  #[Controller( "authenticate" )]
  class AuthenticateController
  {
    #[HttpPost()]
    public function autenticate(
      #[Body( "username" )] string $username,
      #[Body( "password" )] string $password
    ): array {
      return [
        "token" => JWT::encode(
          [
            'iss' => 'http://meu-dominio.com',
            'aud' => 'http://meu-dominio.com',
            'iat' => time(),
            'exp' => time() + 3600,
            'data' => [
              'id' => 123,
              'nome' => $username,
              'email' => $password
            ]
          ], APP_JWT_KEY, "HS256"
        )
      ];
    }
  }
}
