<?php

namespace Websyspro\NestPhp\Lib\Entitys\Base\Controllers
{
  class TestService
  {
    public function __construct(){}
    
    public function execute(): void {
      print_r( "Test Execute" );
    }
  }
}
