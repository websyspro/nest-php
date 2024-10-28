<?php

namespace Websyspro\NestPhp\Lib\Entitys\Defaults
{
  use Attribute;

  #[Attribute(Attribute::TARGET_PROPERTY)]
  class BeforeDefaultDelete
  {
    public function __construct(
      private string | object $class
    ){}

    public function execute(): mixed {
      return [
        "beforeDefaultDelete" => $this->class
      ];
    }
  }
}
