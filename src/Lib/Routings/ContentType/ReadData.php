<?php

namespace Websyspro\NestPhp\Lib\Routings\ContentType
{
use Websyspro\NestPhp\Lib\Routings\ContentType\Enums\ContentMethod;
use Websyspro\NestPhp\Lib\Routings\ContentType\Enums\ContentTypes;
  class ReadData extends ReadUtils
  {
    private array $fieldsArr = [];
    private array $filesArr = [];

    public function __construct(){
      $this->readDataLoad();

      // $fgets = fgets(
      //   $this->contentFile(),
      //   4096
      // );

      // print_r($fgets);
      // print_r($_POST);
      // print_r($_FILES);
    }
    private function readDataLoad(
    ): void {
      if ( $this->contentType() === ContentTypes::FormData->value ) {
        $this->fieldsArr = $this->contentMethod() !== ContentMethod::Post->value
          ? $this->contentLoadDataFile(
            $this->contentFile()
          ) : $_POST;
      }

      print_r( $this->fieldsArr );
    }
  }
}
