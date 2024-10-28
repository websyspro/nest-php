<?php

namespace Websyspro\NestPhp\Lib\Entitys\Base\Controllers
{
  use Websyspro\NestPhp\Lib\Data\DataLoad;
  use Websyspro\NestPhp\Lib\Routings\Decorations\Body;
  use Websyspro\NestPhp\Lib\Routings\Decorations\HttpGet;
  use Websyspro\NestPhp\Lib\Routings\Decorations\HttpPost;
  use Websyspro\NestPhp\Lib\Routings\Decorations\Middlewares\Controller;

  #[Controller( "client" )]
  class ClientController
  {
    #[HttpGet( "byid/:guidid")]
    public function getClientById(
      #[Body( "username" )] mixed $body
    ): mixed {
      return $body;
    }

    #[HttpGet( "test" )]
    public function getClientTest(
    ): mixed {
      return DataLoad::$data;
    }

    #[HttpPost()]
    public function getClientEmpty(
    ): mixed {
      return php_sapi_name();
    }
  }
}
