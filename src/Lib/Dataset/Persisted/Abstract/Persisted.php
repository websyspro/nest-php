<?php

namespace Websyspro\NestPhp\Lib\Dataset\Persisted\Abstract
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  use Websyspro\NestPhp\Lib\Dataset\Persisted\Commons\PersistedUtils;
  use Websyspro\NestPhp\Lib\Entitys\Designs\DesignList;
  use Websyspro\NestPhp\Lib\Dataset\Persisted\Enums\PersistedType;

  class Persisted
  {
    public DesignList $designList;
    public PersistedType $persistedType = PersistedType::Created;

    public function __construct(
      public string $entity,
      public array $entityData
    ){
      $this->setEntity();
      $this->setDataMerge();
      $this->setDataTypes();
    }

    public function entityDefauls(
    ): array {
      return match( $this->persistedType ){
        PersistedType::Created => Utils::mapper(
          $this->designList->events->beforeDefaultCreate,
          fn(mixed $objectEvent): mixed => $objectEvent->execute()
        ),
        PersistedType::Updated => Utils::mapper(
          $this->designList->events->beforeDefaultUpdate,
          fn(mixed $objectEvent): mixed => $objectEvent->execute()
        ),
        PersistedType::Deleted => Utils::mapper(
          $this->designList->events->beforeDefaultDelete,
          fn(mixed $objectEvent): mixed => $objectEvent->execute()
        )
      };
    }

    public function setEntity(
    ): void {
      $this->designList = new DesignList(
        entity: $this->entity
      );
    }

    public function setDataMerge(
    ): void {
      $this->entityData = Utils::ArrayMerge(
        $this->entityData,
        $this->entityDefauls()
      );
    }

    public function setDataTypes(
    ): void {
      $this->entityData = Utils::mapper(
        $this->entityData,
        fn( string | null $value, string $property ): string | null => PersistedUtils::encodeType(
          $value, $this->designList->properties->type(
            $property
          )
        )
      );
    }

    public function getEntity(): string {
      return Utils::EntityName(
        $this->entity
      );
    }

    public function getNamesList(
    ): array {
      return array_keys(
        $this->entityData
      );
    }

    public function getNames(
    ): string {
      return Utils::Join(
        $this->getNamesList()
      );
    }
    
    public function getValues(): string {
      return Utils::join(
        $this->entityData
      );
    }
  }
}
