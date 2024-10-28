<?php

namespace Websyspro\NestPhp\Lib\Entitys\Abstract
{
  class EntityConstraintList
  {
    public function __construct(
      public EntityConstraint $unique,
      public EntityConstraint $index
    ){}
  }
}
