<?php

namespace Websyspro\NestPhp\Lib\Routings\Enums
{
  enum ResponseCode:int {
    case ok = 200;
    case created = 201;
    case accepted = 202;
    case nonAuthoritativeInformation = 203;
    case noContent = 204;
    case badRequest = 400;
    case unauthorized = 401;
    case paymentRequired = 402;
    case forbidden = 403;
    case notFound = 404;
    case methodNotAllowed = 405;
    case notAcceptable = 406;
    case proxyAuthenticationRequired = 407;
    case requestTimeout = 408;
  }
}
