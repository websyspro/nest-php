<?php

namespace Websyspro\NestPhp\Lib\Routings\ContentType
{
use Websyspro\NestPhp\Lib\Routings\ContentType\Enums\ContentMethod;
use Websyspro\NestPhp\Lib\Routings\ContentType\Enums\ContentTypes;
  class ReadData extends ReadUtils
  {
    public array $fieldsArr = [];
    // private array $filesArr = [];

    public function __construct(){
      $this->readDataLoad();
    }
    private function readDataLoad(
    ): void {
      if ( $this->contentType() === ContentTypes::FormData->value ) {
        $this->fieldsArr = $this->contentMethod() !== ContentMethod::Post->value
          ? $this->contentLoadDataFile(
            $this->contentFile()
          ) : $_POST;
      }
    }
  }
}
