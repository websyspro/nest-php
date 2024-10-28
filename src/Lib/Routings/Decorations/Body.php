<?php

namespace Websyspro\NestPhp\Lib\Routings\Decorations
{
  use Attribute;
  use Websyspro\NestPhp\Lib\Data\DataLoad;
  use Websyspro\NestPhp\Lib\Routings\Enums\DecorationType;

  #[Attribute( Attribute::TARGET_PARAMETER )]
  class Body
  {
    public DecorationType $decorationType = DecorationType::RouteParameters;

    public function __construct(
      public string | null $key = null
    ){}

    public function execute(
    ): array | string | object {
      if ( $this->key !== null && isset( DataLoad::$data["BODY"][$this->key] )) {
        return DataLoad::$data["BODY"][$this->key];
      }

      return DataLoad::$data["BODY"];
    }
  }
}
