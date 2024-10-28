<?php

namespace Websyspro\NestPhp\Lib\Dataset\Persisted
{
  use Websyspro\NestPhp\Lib\Database\DB;
  use Websyspro\NestPhp\Lib\Dataset\Persisted\Abstract\Persisted;
  use Websyspro\NestPhp\Lib\Dataset\Persisted\Enums\PersistedType;
    use Websyspro\NestPhp\Lib\Routings\Error;

  class Create extends Persisted
  {
    public PersistedType $persistedType = PersistedType::Created;

    public function setPersisteds(
    ): string {
      $persisteds = DB::query(
        "insert into {$this->getEntity()} ({$this->getNames()}) values({$this->getValues()})"
      );

      if ($persisteds->hasError() ) {
        Error::badRequest(
          $persisteds->getError()
        );
      }

      return "register created with success.";
    }
  }
}
