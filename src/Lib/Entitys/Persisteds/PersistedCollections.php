<?php

namespace Websyspro\NestPhp\Lib\Entitys\Persisteds
{
  use Websyspro\NestPhp\Lib\Database\DB;
  use Websyspro\NestPhp\Lib\Commons\Utils;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\EntityConstraintItem;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\EntityProperty;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\PersistedList;
  use Websyspro\NestPhp\Lib\Entitys\Enums\ConstraintType;
  use Websyspro\NestPhp\Lib\Entitys\Enums\PropertyType;
  use Websyspro\NestPhp\Lib\Entitys\Persisteds\Strings\Scripts;

  class PersistedCollections
  {
    public array $entitys = [];

    public function __construct()
    {
      $this->load();
    }

    private function load(
    ): void {
      $this->loadPersisteds();
      $this->loadPersistedPrimaryKey();
      $this->loadPersistedAutoInc();
      $this->loadPersistedRequireds();
      $this->loadPersistedProperties();
      $this->loadPersistedConstraints();
      $this->loadPersistedForeigns();
    }

    private function loadEnvs(
    ): array {
      parse_str(
        APP_ENVS, $envs
      );
      
      return $envs;
    }

    private function loadEntitys(
    ): array {
      $entitys = DB::query(
        Scripts::scriptEntitys(
          $this->loadEnvs()["name"]
        )
      )->getRows();

      return Utils::mapper(
        $entitys,
        fn( array $row  ) => $row[ "entity" ]
      );
    }

    private function loadProperties(
    ): array {
      $db = DB::query(
        Scripts::scriptProperties(
          $this->loadEnvs()["name"]
        )
      );
      
      if ( $db->hasError() === false ) {
        return Utils::mapper(
          $db->getRows(),
          function( array $properties ) {
            if ( isset( $properties["autoinc"] )) {
              $properties["autoinc"] = $properties["autoinc"] === "1";
            }
            if ( isset( $properties["required"] )) {
              $properties["required"] = $properties["required"] === "1";
            }
            if( isset( $properties["primarykey"] )) {
              $properties["primarykey"] = $properties["primarykey"] === "1";
            }

            return $properties;
          }
        );
      } else { return []; }
    }

    private function loadPersisteds(
    ): void {
      $this->entitys = Utils::mapper(
        $this->loadEntitys(),
        fn( string $entity ) => new PersistedList( $entity )
      );
    }

    private function loadPersistedPrimaryKey(
    ): void {
      Utils::mapper( $this->entitys, function( PersistedList $persisted ){
        Utils::mapper( $this->loadProperties(), function( array $properties ) use( $persisted ) {
          [ "entity" => $entity, "name" => $name ] = $properties;

          if ( $persisted->entity === $entity && isset($properties[ PropertyType::PrimaryKey->value ]) && $properties[ PropertyType::PrimaryKey->value ] === true) {
            $persisted->primarykeys->add( $name );
          }
        });
      });
    }

    private function loadPersistedAutoInc(
    ): void {
      Utils::mapper( $this->entitys, function( PersistedList $persisted ) {
        Utils::mapper( $this->loadProperties(), function( array $properties ) use( $persisted ) {
          [ "entity" => $entity, "name" => $name ] = $properties;

          if ( $persisted->entity === $entity && isset($properties[ PropertyType::AutoInc->value ]) && $properties[ PropertyType::AutoInc->value ] === true) {
            $persisted->autoincs->add( $name );
          }
        });
      });
    }

    private function loadPersistedRequireds(
    ): void {
      Utils::mapper( $this->entitys, function( PersistedList $persisted ) {
        Utils::mapper( $this->loadProperties(), function( array $properties ) use( $persisted ) {
          [ "entity" => $entity, "name" => $name ] = $properties;

          if ($persisted->entity === $entity && isset($properties[ PropertyType::Required->value ]) && $properties[ PropertyType::Required->value ] === true) {
            $persisted->requireds->add( $name );
          }
        });
      });
    }

    private function loadPersistedProperties(
    ): void {
      Utils::mapper( $this->entitys, function( PersistedList $persisted ) {
        Utils::mapper( $this->LoadProperties(), function( array $properties ) use( $persisted ) {
          [ "entity" => $entity, "name" => $key, "type" => $type ] = $properties;

          if ($persisted->entity === $entity) {
            $persisted->properties->add(
              entityDesignProperty: new EntityProperty(
                $key,
                $type,
                $persisted->autoincs,
                $persisted->requireds,
                $persisted->primarykeys
              )
            );
          }
        });
      });
    }

    private function loadConstraints(
      string $entity
    ): array {
      $db = DB::query(
        Scripts::scriptConstraints(
          $this->LoadEnvs()["name"],
          $entity
        )
      );

      if ( $db->hasError() === false ) {
        return Utils::reduce($db->getRows(), [], function( array $reduce, array $constraint ){
          [ "constraint_name" => $constraint_name,
            "property" => $property
          ] = $constraint;
          
          $reduce[ $constraint_name ][] = $property;
          return $reduce;
        });
      } else { return []; }
    }

    private function loadPersistedConstraints(
    ): void {
      Utils::mapper( $this->entitys, function( PersistedList $persisted ){
        Utils::mapper( $this->loadConstraints( $persisted->entity ), function( array $properties, string $constraintType ) use ( $persisted ) {
          if ( preg_match("/^Idx_/", $constraintType) ) {
            $persisted->constraints->index->addGroup($properties);
          }
          if ( preg_match("/^Unq_/", $constraintType) ) {
            $persisted->constraints->unique->addGroup($properties);
          }
        });

        $persisted->constraints->unique->items = Utils::mapper( $persisted->constraints->unique->items,
          fn( array $items ) => new EntityConstraintItem($persisted->entity, $items, ConstraintType::Unique->value));
        $persisted->constraints->index->items  = Utils::mapper( $persisted->constraints->index->items,
          fn( array $items ) => new EntityConstraintItem($persisted->entity, $items, ConstraintType::Index->value));
      });
    }

    private function loadForeigns(
      ): array {
        $db = DB::query(
          Scripts::scriptForeigns(
            $this->LoadEnvs()["name"]
          )
        );
  
        if ( $db->hasError() === false ) {
          return Utils::mapper(
            $db->getRows(),
            function( array $foreigns ) {
            [ , $entity, $key, , $reference, $referenceKey ] = explode(
              "_", $foreigns[ "foreign_name" ]
            );

            return [
              "key" => $key,
              "entity" => $entity,
              "reference" => $reference,
              "referenceKey" => $referenceKey
            ];
          });
        } else { return []; }
      }

    private function loadPersistedForeigns(
    ): void {
      Utils::mapper( $this->entitys, function( PersistedList $persisted ) {
        Utils::mapper( $this->loadForeigns(), function( array $properties ) use( $persisted ) {
          [ "entity" => $entity, "key" => $key ] = $properties;

          if ($persisted->entity === $entity) {
            $persisted->foreigns->add(
              properties: [ "foreign" => $properties ],
              entity: $entity,
              key: $key
            );
          }
        });
      });
    }
  }
}
