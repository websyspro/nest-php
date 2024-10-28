<?php

namespace Websyspro\NestPhp\Lib\Entitys
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  use Websyspro\NestPhp\Lib\Database\DB;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\EntityConstraintItem;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\EntityForeign;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\EntityProperty;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\PersistedList;
  use Websyspro\NestPhp\Lib\Entitys\Designs\DesignList;
  use Websyspro\NestPhp\Lib\Entitys\Enums\PropertyEntity;
  use Websyspro\NestPhp\Lib\Entitys\Persisteds\PersistedCollections;
  use Websyspro\NestPhp\Lib\Logger\Message;

  class Collections
  {
    public array $designs = [];
    public array $persisteds = [];

    public function __construct()
    {
      $this->setEntityPersisteds();
    }
    
    public function add(
      array $entitys
    ): void {
      Utils::mapper(
        $entitys,
        fn( string $entity ): DesignList => (
          $this->designs[ Utils::entityName(
            $entity
          )] = new DesignList( $entity )
        )
      );

      if ( Utils::isEmptyArray( $this->designs ) === false ) {
        $this->setEntitysCreateds();
      }
    }

    private function setEntityPersisteds(
    ): void {
      Utils::mapper(
        (new PersistedCollections())->entitys,
        fn( PersistedList $persistedList ): PersistedList => (
          $this->persisteds[ $persistedList->entity ] = $persistedList
        )
      );
    }

    private function existsInPersisteds(
      string $entity
    ): bool {
      return Utils::isEmptyArray(
        Utils::filter(
          $this->persisteds,
          fn( PersistedList $persisted ) => (
            Utils::entityName( $entity ) === $persisted->entity
          )
        )
      ) !== true;
    }

    private function setEntitysCreateds(
    ): void {
      print_r($this->designs);
      Utils::mapper(
        $this->designs,
        function( DesignList $designList ): void {
          if( $this->existsInPersisteds( $designList->entity ) === false ){
            $this->setEntityCreatedTable( $designList );
            $this->setEntityCreatedsConstraints( $designList );
          } else {
            $this->setEntityUpdatedColumns( $designList );
            $this->setEntityAddColumns( $designList );
            $this->setEntityAddConstraintsUnique( $designList );
            $this->setEntityAddConstraintsIndex( $designList );
            $this->setEntityDropColumns( $designList );
            $this->setEntityDropConstraintsUnique( $designList );
            $this->setEntityDropConstraintsIndex( $designList );
          }
        }
      );

      Utils::mapper(
        $this->designs,
        function( DesignList $designList ): void {
          if( $this->existsInPersisteds( $designList->entity ) === false ){
            $this->setEntityCreatedsForeigns( $designList );
          } else {
            $this->setEntityAddForeigns( $designList );
            $this->setEntityDropForeings( $designList );
          }
        }
      );
    }

    private function setQuery(
      string | array $commandSql,
      string $success
    ): void {
      ( $db = DB::query( $commandSql ))->hasError()
        ? Message::Error( $db->getError())
        : Message::Success( $success );
    }

    private function setEntityCreatedTable(
      DesignList $designList
    ): void {
      $this->setQuery(
        $designList->properties->create(),
        "Entity {$designList->getEntity()} created successfully"
      );
    }

    private function setEntityCreatedsConstraintsUnique(
      DesignList $designList
    ): void {
      Utils::mapper(
        Utils::mapper(
        $designList->constraints->unique->items,
        fn( EntityConstraintItem $constraint ): string => $constraint->create()
        ), fn( string $scriptUnique ) => $this->setQuery(
          $scriptUnique,  "Constraint Unique(s) in {$designList->entity} created successfully"
        )
      );
    }

    private function setEntityCreatedsConstraintsIndex(
      DesignList $designList
    ): void {
      Utils::mapper(
        Utils::mapper(
          $designList->constraints->index->items,
          fn( EntityConstraintItem $constraint ): string => $constraint->create()
        ), fn( string $scriptIndex ) => $this->setQuery(
          $scriptIndex, "Constraint Index(s) in {$designList->entity} created successfully"
        )
      );
    }

    private function setEntityCreatedsConstraints(
      DesignList $designList
    ): void {
      $this->setEntityCreatedsConstraintsUnique( $designList );
      $this->setEntityCreatedsConstraintsIndex( $designList );
    }

    private function setEntityCreatedsForeigns(
      DesignList $designList
    ): void {
      Utils::mapper(
        Utils::mapper(
          $designList->foreigns->items,
          fn( EntityForeign $entityForeign ): string => $entityForeign->create()
        ), fn( string $scriptForeign ) => $this->setQuery(
          $scriptForeign,  "Constraint Foreign(s) in {$designList->entity} created successfully"
        )
      );
    }
    
    private function setEntityUpdatedColumns(
      DesignList $designList
    ): void {
      Utils::mapper(
        $this->persisteds[ $designList->entity ]->properties->items,
        function( EntityProperty $item, string $property ) use( $designList ): void {
          if ( $designList->properties->isProperty( $property ) && $designList->properties->items[ $property ]->type !== $item->type && $property !== PropertyEntity::Id->value ){
            $this->setQuery(
              $designList->properties->modifyColumn( $property ),
              "column {$property} in {$designList->getEntity()} updated successfully"
            );
          }
        }
      );
    }

    private function setEntityAddColumns(
      DesignList $designList
    ): void {
      Utils::mapperKey(
        $designList->properties->items,
        function( string $property ) use($designList): void {
        if( in_array( $property, $this->persisteds[ $designList->entity ]->properties->names()) === false ) {
          $this->setQuery( $designList->properties->addColumn(property: $property), "Column {$property} in {$designList->getEntity()} added successfully" );
        }
      });
    }

    private function setEntityAddConstraintsUnique(
      DesignList $designList
    ): void {
      Utils::mapper(
        $this->designs[ $designList->entity ]->constraints->unique->items,
        function( EntityConstraintItem $constraint ) use ( $designList ) {
        if( in_array( $constraint->name(), $this->persisteds[ $designList->entity ]->constraints->unique->names()) === false) {
          $this->setQuery( $constraint->create(), "Constraint Unique {$constraint->name()} in {$constraint->entity} created successfully" );
        }
      });
    }

    private function setEntityAddConstraintsIndex(
      DesignList $designList
    ): void {
      Utils::mapper(
        $this->designs[ $designList->entity ]->constraints->index->items,
        function( EntityConstraintItem $constraint ) use ( $designList ): void {
        if( in_array( $constraint->name(), $this->persisteds[ $designList->entity ]->constraints->index->names()) === false ) {
          $this->setQuery( $constraint->create(), "Constraint Index {$constraint->name()} in {$constraint->entity} created successfully");
        }
      });
    }

    private function setEntityAddForeigns(
      DesignList $designList
    ): void {
      Utils::mapper(
        $designList->foreigns->items,
        function( EntityForeign $entityForeign ) use( $designList ): void {
        if( in_array( $entityForeign->name(), $this->persisteds[$designList->entity]->foreigns->names()) === false ){
          $this->setQuery( $entityForeign->create(), "Constraint Foreign || {$entityForeign->name()} in {$designList->entity} created successfully" );
        }
      });
    }

    private function setEntityDropColumns(
      DesignList $designList
    ): void {
      Utils::mapperKey(
        $this->persisteds[ $designList->entity ]->properties->items,
        function( string $property ) use( $designList ): void {
          if ( $designList->properties->isProperty(property: $property ) === false) {
            $this->setQuery(
              $designList->properties->dropColumn(property: $property),
              "Column {$property} in {$designList->entity} deleted successfully"
            );
          }
        }
      );
    }

    private function setEntityDropConstraintsUnique(
      DesignList $designList
    ): void {
      Utils::mapper(
        $this->persisteds[$designList->entity]->constraints->unique->items,
        function( EntityConstraintItem $entityConstraintItem ) use( $designList ): void {
        if( in_array( $entityConstraintItem->name(), $this->designs[$designList->entity]->constraints->unique->names()) === false) {
            $this->setQuery( $entityConstraintItem->drop(), "Constraint unique {$entityConstraintItem->name()} in {$designList->entity} deleted successfully" );
          }
        }
      );
    }

    private function setEntityDropConstraintsIndex(
      DesignList $designList
    ): void {
      Utils::mapper(
        $this->persisteds[$designList->entity]->constraints->index->items,
        function( EntityConstraintItem $entityConstraintItem ) use( $designList ): void {
          if( in_array( $entityConstraintItem->name(), $this->designs[$designList->entity]->constraints->index->names()) === false ) {
            $this->setQuery( $entityConstraintItem->drop(), "Constraint index {$entityConstraintItem->name()} in {$designList->entity} deleted successfully" );
          }
        }
      );
    }
    
    private function setEntityDropForeings(
      DesignList $designList
    ): void {
      Utils::mapper(
        $this->persisteds[ $designList->entity ]->foreigns->items,
        function( EntityForeign $entityForeign ) use($designList) {
        if ( in_array( needle: $entityForeign->name(), haystack: $designList->foreigns->names()) === false ){
          $this->setQuery( [ $entityForeign->drop(), $entityForeign->dropIndex()], "Constraint Foreign {$entityForeign->name()} in {$designList->entity} deleted successfully" );
        }
      });
    }
  }
}
