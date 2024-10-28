<?php

namespace Websyspro\NestPhp\Lib\Entitys\Constraints
{
  use Attribute;
  use Websyspro\NestPhp\Lib\Entitys\Enums\PropertyEntity;

  #[Attribute(Attribute::TARGET_PROPERTY)]
  class Foreign
  {
    public function __construct(
      private readonly string | object $reference,
      private readonly string $key = PropertyEntity::Id->value
    ){}

    public function execute(
    ): array {
      return [
        "foreign" => [
          "reference" => $this->reference,
          "referenceKey" => $this->key
        ]
      ];
    }
  }
}
