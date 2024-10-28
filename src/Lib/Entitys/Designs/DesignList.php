<?php

namespace Websyspro\NestPhp\Lib\Entitys\Designs
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\EntityAutoIncList;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\EntityConstraint;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\EntityConstraintItem;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\EntityConstraintList;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\EntityEvents;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\EntityForeignList;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\EntityPrimaryKeyList;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\EntityProperty;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\EntityPropertyList;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\EntityRequiredList;
  use Websyspro\NestPhp\Lib\Entitys\Consts\PropertyOrder;
  use Websyspro\NestPhp\Lib\Entitys\Enums\ConstraintType;
  use Websyspro\NestPhp\Lib\Entitys\Enums\PropertyType;
  use Websyspro\NestPhp\Lib\Entitys\Interfaces\EntityInterface;
  use Websyspro\NestPhp\Lib\Reflections\ClassLoader;
  
  class DesignList implements EntityInterface
  {
    public EntityRequiredList $requireds;
    public EntityAutoIncList $autoincs;
    public EntityForeignList $foreigns;
    public EntityConstraintList $constraints;
    public EntityPropertyList $properties;
    public EntityPrimaryKeyList $primarykeys;
    public EntityEvents $events;

    public function __construct(
      public string | object $entity,
    ){
      $this->setClassLoaderInit();
      $this->setClassLoaderPrimaryKey();
      $this->setClassLoaderAutoInc();
      $this->setClassLoaderRequireds();
      $this->setClassLoaderProperties();
      $this->setClassLoaderConstraints();
      $this->setClassLoaderForeigns();
      $this->setClassLoaderEvents();
      $this->setClassLoaderName();
    }

    public function getClassLoad(
    ): ClassLoader {
      return new ClassLoader(
        $this->entity
      );
    }

    public function getEntity(
    ): string {
      return Utils::entityName(
        $this->entity
      );
    }

    public function setClassLoaderInit(
    ): void {
      $this->foreigns = new EntityForeignList;
      $this->autoincs = new EntityAutoIncList;
      $this->requireds = new EntityRequiredList;
      $this->primarykeys = new EntityPrimaryKeyList;
      $this->properties = new EntityPropertyList( Utils::EntityName($this->entity));
      $this->constraints = new EntityConstraintList( new EntityConstraint(), new EntityConstraint());
    }

    public function setPropertiesOrders(
      array $properties = []
    ): array {
      return array_merge(
        Utils::filterKey(
          $properties,
          fn( $key ) => in_array(
            $key,
            PropertyOrder::$initial
          )
        ),
        Utils::filterKey(
          $properties,
          fn( $key ) => !in_array(
            $key, array_merge(
              PropertyOrder::$initial, PropertyOrder::$finaly
            )
          )
        ),
        Utils::filterKey(
          $properties,
          fn( $key ) =>  in_array(
            $key,
            PropertyOrder::$finaly
          )
        ),
      );
    }

    public function setClassLoaderProperties(
    ): void {
      Utils::mapper(
        $this->setPropertiesOrders(
        $this->getClassLoad()->properties
      ), function(array $properties, string $key) {
          [ "type" => $type ] = $properties;

          $this->properties->add(
            entityDesignProperty: new EntityProperty(
              $key,
              $type,
              $this->autoincs,
              $this->requireds,
              $this->primarykeys
            )
          );
        }
      );
    }

    public function setClassLoaderPrimaryKey(
    ): void {
      Utils::mapper(
        $this->getClassLoad()->properties,
        function( array $property, string $key ) {
          if ( isset( $property[ PropertyType::PrimaryKey->value ]) ) {
            $this->primarykeys->add( $key );
          }
        }
      );
    }

    public function setClassLoaderAutoInc(): void {
      Utils::mapper(
        $this->getClassLoad()->properties,
        function( array $property, string $key ) {
          if ( isset( $property[ PropertyType::AutoInc->value ]) ) {
            $this->autoincs->add( $key );
          }
        }
      );
    }

    public function setClassLoaderRequireds(
    ): void {
      Utils::mapper(
        $this->getClassLoad()->properties,
        function( array $property, string $key ) {
          if ( isset( $property[ PropertyType::Required->value ]) ) {
            $this->requireds->add( $key );
          }
        }
      );
    }

    public function hasConstraints(
      array $properties = []
    ): bool {
      return in_array( ConstraintType::Index->value,  array_keys( $properties ))
          || in_array( ConstraintType::Unique->value, array_keys( $properties ));
    }

    public function setClassLoaderConstraints(
    ): void {
      Utils::filter( $this->getClassLoad()->properties, function( array $properties, string $key ) {
        if ($this->hasConstraints( $properties )) {
          if (in_array( ConstraintType::Index->value,  array_keys( $properties )) ) {
            $this->constraints->index->add(
              constraintOrder: $properties[ConstraintType::Index->value],
              name: $key
            );
          }
          if ( in_array( ConstraintType::Unique->value,  array_keys( $properties )) ) {
            $this->constraints->unique->add(
              constraintOrder: $properties[ConstraintType::Unique->value],
              name: $key
            );
          }
        }
      });

      $this->constraints->unique->items = Utils::mapper( $this->constraints->unique->items,
        fn( array $items ) => new EntityConstraintItem( Utils::entityName($this->entity), $items, ConstraintType::Unique->value ));
      $this->constraints->index->items  = Utils::mapper( $this->constraints->index->items,
        fn( array $items ) => new EntityConstraintItem( Utils::entityName($this->entity), $items, ConstraintType::Index->value ));
    }

    public function setClassLoaderForeigns(
    ): void {
      Utils::mapper(
        Utils::filter( $this->getClassLoad()->properties,
          fn(array $properties) => isset( $properties[ PropertyType::Foreign->value ] )
        ), fn( array $properties, string $key ) => $this->foreigns->add(
          entity: Utils::EntityName($this->entity), properties: $properties, key: $key
        )
      );
    }

    public function setClassLoaderEvents(
    ): void {
      $this->events = new EntityEvents($this->entity);
    }

    private function setClassLoaderName(): void {
      $this->entity = Utils::entityName($this->entity);
    }
  }
}
