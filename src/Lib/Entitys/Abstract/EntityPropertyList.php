<?php

namespace Websyspro\NestPhp\Lib\Entitys\Abstract
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  
  class EntityPropertyList
  {
    public array $items = [];

    public function __construct(
      public string $entity
    ){}

    public function add(
      EntityProperty $entityDesignProperty
    ): void {
      $this->items[
        $entityDesignProperty->name
      ] = $entityDesignProperty;
    }

    public function names(
    ): array {
      return array_keys(
        $this->items
      );
    }

    public function type(
      string $property,
      bool $fullType = true
    ): string {
      $property = preg_replace(
        [
          "/^sum\(/", "/\)$/"
        ],
        "",
        $property
      );

      if ( isset( $this->items[ $property ]) === false ) {
        return "";
      }


      return $fullType === true
        ? $this->getSingleType(
          $this->items[ $property ]->type
        ) : $this->items[ $property ]->type;
    }

    public function after(
      string $property
    ): string {
      return $this->names()[
        array_search(
          $property, $this->names()
        ) - 1
      ];
    }

    private function getSingleType(
      string $typeStructure
    ): string {
      return trim( preg_replace(
        [ "/[0-9]/","/\(/","/\)/", "/,/" ], "", Utils::ShitArray(
          explode(" ", $typeStructure )
        )
      ));
    }

    public function types(
    ): array {
      return Utils::Mapper(
        $this->items, fn(
          EntityProperty $entityDesignProperty
        ) => $this->getSingleType(
          $entityDesignProperty->type
        )
      );
    }
    
    private function scriptCreate(
      string $entity,
      array $properties = []
    ): string {
      return Utils::Join(
        array_merge([
          "create table if not exists {$entity} ("
        ], [ Utils::Join($properties, ", ") ], [ ") engine=innodb" ]), ""
      );
    }

    public function isProperty(
      string $property
    ): bool {
      return in_array(
        $property, $this->names()
      );
    }

    public function create(
    ): string {
      return $this->scriptCreate(
        $this->entity, Utils::Mapper(
          $this->items, fn(
            EntityProperty $entityDesignProperty
          ) => Utils::Join([
            trim( $entityDesignProperty->name ),
            trim( $entityDesignProperty->type )
          ], " ")
        )
      );
    }

    private function scriptAddCoolumn(
      string $property,
      string $type,
      string $after
    ): string {
      return "alter table {$this->entity} add column {$property} {$type} after {$after}";
    }

    public function addColumn(
      string $property
    ): string {
      return $this->scriptAddCoolumn(
        $property, $this->type($property, false), $this->after($property)
      );
    }

    private function scriptModifyCoolumn(
      string $property,
      string $type
    ): string {
      return "alter table {$this->entity} modify column {$property} {$type}";
    }

    public function modifyColumn(
      string $property
    ): string {
      return $this->scriptModifyCoolumn(
        $property, $this->type($property, false)
      );
    }

    private function scriptDropCoolumn(
      string $property
    ): string {
      return "alter table {$this->entity} drop column {$property}";
    }

    public function dropColumn(
      string $property
    ): string {
      return $this->scriptDropCoolumn(
        $property
      );
    }
  }
}
