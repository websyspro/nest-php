<?php

namespace Websyspro\NestPhp\Lib\Entitys\Abstract
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  use Websyspro\NestPhp\Lib\Reflections\ClassAttributes;
  use Websyspro\NestPhp\Lib\Reflections\ClassLoader;

  class EntityEvents
  {
    public array $beforeDefaultCreate = [];
    public array $beforeDefaultUpdate = [];
    public array $beforeDefaultDelete = [];

    public function __construct(
      public string | object $entity
    ){
      $this->setClassLoaderCreateEvents();
      $this->setClassLoaderUpdateEvents();
      $this->setClassLoaderDeleteEvents();
    }

    private function getClassLoad(
    ): ClassLoader {
      return new ClassLoader(
        $this->entity
      );
    }
    
    private function setClassLoaderEventType(
      string $eventType
    ): void {
      Utils::mapper(
        $this->getClassLoad()->properties,
        function( array $properties, string $key ) use( $eventType ) {
          if ( in_array( $eventType, array_keys( $properties ))) {
            [ $eventType => $beforeDefaultCreate ] = $properties;
            
            $this->{ $eventType }[ $key ] = (
              new ClassAttributes( $beforeDefaultCreate )
            )->New();
          }
        }
      );
    }

    private function setClassLoaderCreateEvents(
    ): void {
      $this->setClassLoaderEventType( "beforeDefaultCreate" );
    }

    private function setClassLoaderUpdateEvents(
    ): void {
      $this->setClassLoaderEventType( "beforeDefaultUpdate" );
    }
    
    private function setClassLoaderDeleteEvents(
    ): void {
      $this->setClassLoaderEventType( "beforeDefaultDelete" );
    }
  }
}
