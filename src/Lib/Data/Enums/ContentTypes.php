<?php

namespace Websyspro\NestPhp\Lib\Data\Enums
{
  enum ContentTypes:string {
    case MultipartFormData = "multipart/form-data";
    case MultipartFormDataUrlencoded = "application/x-www-form-urlencoded";
    case ApplicationJSON = "application/json";
  }
}
