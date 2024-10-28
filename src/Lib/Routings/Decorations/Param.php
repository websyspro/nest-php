<?php

namespace Websyspro\NestPhp\Lib\Routings\Decorations
{
  use Attribute;
  use Websyspro\NestPhp\Lib\Data\DataLoad;
  use Websyspro\NestPhp\Lib\Routings\Enums\DecorationType;

  #[Attribute( Attribute::TARGET_PARAMETER )]
  class Param
  {
    public DecorationType $decorationType = DecorationType::RouteParameters;

    public function __construct(
      public string | null $key = null
    ){}

    public function execute(
    ): array | string {
      if ( $this->key !== null ) {
        if ( isset( DataLoad::$data["PARAM"][$this->key] )){
          return DataLoad::$data["PARAM"][$this->key];
        }
      }

      return DataLoad::$data["QUERY"];
    }
  }
}
