<?php

namespace Websyspro\NestPhp\Lib\Entitys\Abstract
{
  class EntityAutoIncList
  {
    public array $items = [];

    public function add(
      string $property
    ): void {
      $this->items[] = $property;
    }

    public function isAutoInc(
      string $property
    ): bool {
      return in_array(
        $property, $this->items
      );
    }
  }
}
