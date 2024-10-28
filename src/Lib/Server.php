<?php

namespace Websyspro\NestPhp\Lib
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  use Websyspro\NestPhp\Lib\Data\DataLoad;
  use Websyspro\NestPhp\Lib\Entitys\Collections;
  use Websyspro\NestPhp\Lib\Logger\Message;
  use Websyspro\NestPhp\Lib\Routings\ControllerDetails;
  use Websyspro\NestPhp\Lib\Routings\Controllers;
  use Websyspro\NestPhp\Lib\Routings\Controllers\AuthenticateController;
  use Websyspro\NestPhp\Lib\Routings\Controllers\ContextController;
  use Websyspro\NestPhp\Lib\Routings\Response;

  class Server
  {
    private Controllers $controllers;
    public static array $contexts;

    public function __construct(
      private array $controllersArr,
      private array $entitysArr,
      private array $contextsArr,
    ){
      $this->args();
      $this->init();
      $this->search();
    }

    private function init(
    ): void {
      if ( $this->isArgc() === false ) {
        Server::ctts();
        Message::Start();
        DataLoad::create();
      }
    }

    private function ctts(
    ): void {
      static::$contexts = Utils::mapper(
        $this->contextsArr,
        fn( string $context ) => $context
      );
    }

    private function isArgc(
    ): bool {
      if ( isset( $_SERVER[ "argc" ] ) === false ) {
        return false;
      }

      [ "argc" => $argc ] = $_SERVER;
      return $argc === 2;
    }

    private function args(
    ): void {
      if ( $this->isArgc() === true ) {
        [ "argv" => $argv ] = $_SERVER;

        Utils::mapper(
          array_slice(
            Utils::mapper(
              (array)$argv,
              fn( string $command ) => (
                preg_replace(
                  "/^-{2}/",
                  "",
                  $command
                )
              )),
            1
          ), function( string $command ){
            [ $executed, $isExecuted ] = explode(
              "=", $command
            );
            
            if ( $executed === "migration" ){
              $this->migration( $isExecuted );
            }
          }
        );
      }
    }

    private function migration(
      string $isExecuted
    ): void {
      if ( $isExecuted === "yes" || $isExecuted === "1" ) {
        ( new Collections())->add(
          $this->entitysArr
        );
      }
    }

    private function isSearch(
    ): bool {
      return $this->controllers->count() !== 0;
    }

    private function search(
    ): void {
      if ( $this->isArgc() === false ) {
        $this->controllers = new Controllers(
          Utils::mapper(
            array_merge(
              $this->controllersArr, [
                AuthenticateController::class,
                ContextController::class
              ]
            ),
            fn( string $controller ) => (
              new ControllerDetails( $controller )
            )
          ),
          new Response()
        );
  
        if ( $this->isSearch() ) {
          $this->controllers->search();
        }
      }
    }

    public static function create(
      array $controllers = [],
      array $entitys = [],
      array $contexts = []
    ): Server {
      return new static(
        $controllers,
        $entitys,
        $contexts
      );
    }
  }
}
