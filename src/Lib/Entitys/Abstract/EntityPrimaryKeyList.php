<?php

namespace Websyspro\NestPhp\Lib\Entitys\Abstract
{
  class EntityPrimaryKeyList
  {
    public array $items = [];

    public function add(
      string $key
    ): void {
      $this->items[] = $key;
    }

    public function isPrimaryKey(
      string $property
    ): bool {
      return in_array(
        $property,
        $this->items
      );
    }

    public function names(
    ): array {
      return array_values(
        $this->items
      );
    }
  }
}
