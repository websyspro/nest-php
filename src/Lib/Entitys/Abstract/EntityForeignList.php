<?php

namespace Websyspro\NestPhp\Lib\Entitys\Abstract
{
  use Websyspro\NestPhp\Lib\Commons\Utils;

  class EntityForeignList
  {
    public array $items = [];

    public function add(
      array $properties,
      string $entity,
      string $key
    ): void {
      [ "reference" => $reference,
        "referenceKey" => $referenceKey
      ] = $properties[ "foreign" ];

      $this->items[] = new EntityForeign(
        referenceKey: $referenceKey,
        reference: Utils::EntityName( $reference ),
        entity: Utils::EntityName( $entity ),
        key: $key
      );
    }

    public function names(
    ): array {
      return Utils::Mapper( $this->items, fn(EntityForeign $entityForeign) => $entityForeign->name());
    }
  }
}
