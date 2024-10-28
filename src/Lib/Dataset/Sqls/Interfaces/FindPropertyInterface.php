<?php

namespace Websyspro\NestPhp\Lib\Dataset\Sqls\Interfaces
{
use Websyspro\NestPhp\Lib\Dataset\Sqls\Enums\FindType;
  class FindPropertyInterface
  {
    public function __construct(
      public array $properties = [],
      public FindType $findType = FindType::FindAnd
    ){}
  }
}
