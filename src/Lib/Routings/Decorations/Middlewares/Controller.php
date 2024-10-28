<?php

namespace Websyspro\NestPhp\Lib\Routings\Decorations\Middlewares
{
  use Attribute;
  use Websyspro\NestPhp\Lib\Routings\Enums\DecorationType;

  #[Attribute( Attribute::TARGET_CLASS )]
  class Controller
  {
    public DecorationType $decorationType = DecorationType::Controller;

    public function __construct(
      public string $name
    ){}
  }
}
