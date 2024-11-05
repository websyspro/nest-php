<?php

namespace Websyspro\NestPhp\Lib\Dataset\Persisted
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  use Websyspro\NestPhp\Lib\Database\DB;
  use Websyspro\NestPhp\Lib\Dataset\Persisted\Abstract\Persisted;
  use Websyspro\NestPhp\Lib\Dataset\Persisted\Enums\PersistedType;
  use Websyspro\NestPhp\Lib\Routings\Error;

  class Update extends Persisted
  {
    private array $wheresList = [];
    private array $updatesList = [];

    public PersistedType $persistedType = PersistedType::Updated;

    public function setUpdateList(
    ): void {
      $this->updatesList = Utils::filter(
        $this->entityData,
        fn( string $_, string $key ): bool => in_array(
          needle: $key, haystack: $this->designList->primarykeys->names()
        ) === false
      );
    }

    public function setWheresList(
    ): void {
      $this->wheresList = Utils::filter(
        $this->entityData,
        fn( string $_, string $key ): bool => in_array(
          needle: $key, haystack: $this->designList->primarykeys->names()
        ) === true
      );
    }
    
    public function getUpdatesValue(
    ): string {
      return Utils::join(
        Utils::mapper(
          $this->updatesList,
          fn( string $value, string $property ): string => "{$property}={$value}"
        )
      );
    }

    public function getWheresValue(
    ): string {
      return Utils::join(
        Utils::mapper(
          $this->wheresList,
          fn( string $value, string $property ): string => "{$property}={$value}"
        )
      );
    }

    public function setPersisteds(
    ): int {
      $this->setUpdateList();
      $this->setWheresList();

      $entityRow = DB::query(
        "select deleted from {$this->getEntity()} where {$this->getWheresValue()}"
      )->getRow();

      if( (int)$entityRow[ "deleted" ] === 1 ) {
        Error::badRequest( "register not found" );
      }

      $persisteds = DB::query(
        "update {$this->getEntity()} set {$this->getUpdatesValue()} where {$this->getWheresValue()}"
      );

      if ($persisteds->hasError() ) {
        Error::badRequest(
          $persisteds->getError()
        );
      }

      return 1;
    }
  }
}
