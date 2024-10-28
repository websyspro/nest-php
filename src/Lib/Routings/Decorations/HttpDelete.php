<?php

namespace Websyspro\NestPhp\Lib\Routings\Decorations
{
  use Attribute;
  use Websyspro\NestPhp\Lib\Routings\Enums\DecorationType;
  use Websyspro\NestPhp\Lib\Routings\Enums\HttpType;

  #[Attribute( Attribute::TARGET_METHOD )]
  class HttpDelete
  {
    public DecorationType $decorationType = DecorationType::Route;
    public HttpType $httpType = HttpType::Delete;

    public function __construct(
      public string $route = ""
    ){}
  }
}
