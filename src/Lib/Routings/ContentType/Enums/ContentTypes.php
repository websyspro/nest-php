<?php

namespace Websyspro\NestPhp\Lib\Routings\ContentType\Enums
{
  enum ContentTypes: string {
    case FormData = "multipart/form-data";
    case ApplicationXWWWFormUrlEncoded = "application/x-www-form-urlencoded";
    case TextPlain = "text/plain";
    case TextHtml = "text/html";
    case ApplicationJavascript = "application/javascript";
    case ApplicationJson = "application/json";
    case ApplicationXml = "application/xml";
  }
}
