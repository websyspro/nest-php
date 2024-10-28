<?php

namespace Websyspro\NestPhp\Lib\Entitys\Base\Contexts
{
  use Websyspro\NestPhp\Lib\Dataset\Repository;
  
  interface ContextInterface
  {
    public function repository(): Repository;
  }
}
