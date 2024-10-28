<?php

namespace Websyspro\NestPhp\Lib\Routings\Controllers
{
  use Websyspro\NestPhp\Lib\Entitys\Base\Contexts\Context;
  use Websyspro\NestPhp\Lib\Routings\Decorations\Body;
    use Websyspro\NestPhp\Lib\Routings\Decorations\HttpDelete;
    use Websyspro\NestPhp\Lib\Routings\Decorations\HttpGet;
  use Websyspro\NestPhp\Lib\Routings\Decorations\HttpPost;
    use Websyspro\NestPhp\Lib\Routings\Decorations\HttpPut;
    use Websyspro\NestPhp\Lib\Routings\Decorations\Middlewares\Authenticate;
  use Websyspro\NestPhp\Lib\Routings\Decorations\Middlewares\Controller;

  #[Controller( "context" )]
  class ContextController
  {
    #[HttpGet()]
    #[Authenticate()]
    public function contextList(
      #[Body( "name" )] string $name,
      #[Body( "params" )] object $params
    ): array {
      return ( new Context )->browser(
        $name, $params
      );
    }

    #[HttpPost()]
    #[Authenticate()]
    public function contextCreate(
      #[Body( "name" )] string $name,
      #[Body( "rows" )] array $rows
    ): mixed {
      return ( new Context )->create(
        $name, $rows
      );
    }

    #[HttpPut()]
    #[Authenticate()]
    public function contextUpdate(
      #[Body( "name" )] string $name,
      #[Body( "rows" )] array $rows
    ): mixed {
      return ( new Context )->update(
        $name, $rows
      );
    }

    #[HttpDelete()]
    #[Authenticate()]
    public function contextDelete(
      #[Body( "name" )] string $name,
      #[Body( "rows" )] array $rows
    ): mixed {
      return ( new Context )->delete(
        $name, $rows
      );
    }
  }
}
