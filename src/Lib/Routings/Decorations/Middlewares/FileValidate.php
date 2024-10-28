<?php

namespace Websyspro\NestPhp\Lib\Routings\Decorations\Middlewares
{
  use Attribute;
  use Websyspro\NestPhp\Lib\Routings\Enums\DecorationType;

  #[Attribute( Attribute::TARGET_CLASS )]
  class FileValidate
  {
    public DecorationType $decorationType = DecorationType::Middleware;

    public function __construct(
      private array $extensions = [],
      private int $filesize = 0
    ){}

    public function execute(
    ): array {
      return [];
    }
  }
}
