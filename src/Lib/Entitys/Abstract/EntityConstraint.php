<?php

namespace Websyspro\NestPhp\Lib\Entitys\Abstract
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  use Websyspro\NestPhp\Lib\Entitys\Enums\ConstraintType;

  class EntityConstraint
  {
    public array $items = [];

    public function add(
      int $constraintOrder,
      string $name,
    ): void {
      $this->items[
        $constraintOrder
      ][] = $name;
    }

    public function addGroup(
      array $name
    ): void {
      $this->items[
        sizeof($this->items) + 1
      ] = $name;
    }

    public function names(
    ): array {
      return Utils::Mapper($this->items, function( EntityConstraintItem $item ){
        return $item->name();
      });
    }
  }
}
