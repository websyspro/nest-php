<?php

namespace Websyspro\NestPhp\Lib\Dataset\Persisted
{
  class Status
  {
    public function __construct(
      public bool $error,
      public string $texto
    ){}
  }
}
