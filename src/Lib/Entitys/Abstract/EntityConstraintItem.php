<?php

namespace Websyspro\NestPhp\Lib\Entitys\Abstract
{

    use Websyspro\NestPhp\Lib\Commons\Utils;
    use Websyspro\NestPhp\Lib\Entitys\Enums\ConstraintType;

  class EntityConstraintItem
  {
    public function __construct(
      public string $entity,
      public array $propertys,
      public string $type
    ){}

    public function getList(
    ): string {
      return Utils::Join(
        $this->propertys, "_"
      );
    }

    public function getNamesList(
    ): string {
      return Utils::Join(
        $this->propertys, ","
      );
    }

    public function name(
    ): string {
      if ( $this->type === ConstraintType::Index->value ) {
        return "Idx_{$this->entity}_in_{$this->getList()}";
      } else {
        return "Unq_{$this->entity}_in_{$this->getList()}";
      }
    }

    private function scriptAddUnique(
    ): string {
      return "alter table {$this->entity} add unique {$this->name()} ({$this->getNamesList()})";
    }

    private function scriptAddIndex(
    ): string {
      return "alter table {$this->entity} add index {$this->name()} ({$this->getNamesList()})";
    }

    public function create(
    ): string {
      return $this->type === ConstraintType::Index->value
        ? $this->scriptAddIndex()
        : $this->scriptAddUnique();
    }

    private function dropIndex(
    ): string {
      return "alter table {$this->entity} drop index {$this->name()}";
    }

    private function dropUnique(
    ): string {
      return "alter table {$this->entity} drop index {$this->name()}";
    }

    public function drop(
    ): string {
      return $this->type === ConstraintType::Index->value
        ? $this->dropIndex()
        : $this->dropUnique();
    }
  }
}
