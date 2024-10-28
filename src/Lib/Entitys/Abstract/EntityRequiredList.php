<?php

namespace Websyspro\NestPhp\Lib\Entitys\Abstract
{
  class EntityRequiredList
  {
    public array $items = [];

    public function add(
      string $property
    ): void {
      $this->items[] = $property;
    }

    public function isRequired(
      string $property
    ): bool {
      return in_array(
        $property, $this->items
      );
    }
  }
}
