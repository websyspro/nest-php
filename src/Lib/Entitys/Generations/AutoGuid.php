<?php

namespace Websyspro\NestPhp\Lib\Entitys\Generations
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  
  class AutoGuid
  {
    public function execute(): string {
      return Utils::Guid();
    }
  }
}
