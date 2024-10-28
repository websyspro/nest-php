<?php

namespace Websyspro\NestPhp\Lib\Logger
{
  class Message
  {
    private static mixed $startTime;
    private static mixed $diffTime;
    private static mixed $endTime;
    private static int $scale = 4;

    public static function success(
      string $context
    ): void {
      printf( "\x1b[32mWebsyspro\NestPHP - \x1b[37m%s \x1b[37mLog\x1b[37m - {$context} +\x1b[33m%sms\x1b[37m" . PHP_EOL,
        static::CurrentDatetime(),
        static::CurrentMicrotime()
      );
    }

    public static function error(
      string $context
    ): void {
      printf( "\x1b[32mWebsyspro\NestPHP - \x1b[37m%s \x1b[33mErr\x1b[37m - {$context} +\x1b[33m%sms\x1b[37m" . PHP_EOL,
        static::CurrentDatetime(),
        static::CurrentMicrotime()
      );
    }

    public static function start(): void {
      static::$startTime = microtime(true);
    }

    private static function currentDatetime(): string {
      return date("h:i:s d/m/Y");
    }

    private static function setEndTime(
    ): void {
      static::$endTime = microtime(true);
    }

    private static function setDiffTime(
    ): void {
      static::$diffTime = bcmul(bcsub(
        static::$endTime,
        static::$startTime,
        static::$scale
      ), 10000, 0);
    }

    private static function setStartTime(
    ): void {
      static::$startTime = static::$endTime;
    }

    private static function getDiffTime(
    ): string {
      return static::$diffTime;
    }

    private static function currentMicrotime(
    ): string {
      static::SetEndTime();
      static::SetDiffTime();
      static::SetStartTime();
      return static::GetDiffTime();
    }
  }
}
