<?php

namespace Websyspro\NestPhp\Lib\Entitys\Abstract
{
  class EntityForeign
  {
    public function __construct(
      public string $entity,
      public string $referenceKey,
      public string $reference,
      public string $key
    ){}

    public function name(
    ): string {
      return "FK_{$this->entity}_{$this->key}_in_{$this->reference}_{$this->referenceKey}";
    }

    private function scriptAddForeign(
    ): string {
      return "alter table {$this->entity} add constraint {$this->name()} foreign key ({$this->key}) references {$this->reference} ({$this->referenceKey})";
    }
    
    public function create(
    ): string {
      return $this->scriptAddForeign();
    }

    public function scriptDrop(
    ): string {
      return "alter table {$this->entity} drop foreign key {$this->name()}";
    }
    
    public function drop(
    ): string {
      return $this->scriptDrop();
    }

    public function scriptDropIndex(
    ): string {
      return "alter table {$this->entity} drop index {$this->name()}";
    }

    public function dropIndex(
    ): string {
      return $this->scriptDropIndex();
    }
  }
}
