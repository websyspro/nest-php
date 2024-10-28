<?php

namespace Websyspro\NestPhp\Lib\Entitys\Generations
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  
  class AutoDate
  {
    public function execute(): string {
      return Utils::Now();
    }
  }
}
