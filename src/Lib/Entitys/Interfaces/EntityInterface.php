<?php

namespace Websyspro\NestPhp\Lib\Entitys\Interfaces
{
  use Websyspro\NestPhp\Lib\Reflections\ClassLoader;

  interface EntityInterface
  {
    public function getClassLoad(): ClassLoader;
    public function setClassLoaderInit(): void;
    public function setClassLoaderProperties(): void;
    public function setClassLoaderPrimaryKey(): void;
    public function setClassLoaderAutoInc(): void;
    public function setClassLoaderRequireds(): void;
    public function setClassLoaderConstraints(): void;
    public function setClassLoaderForeigns(): void;
    public function setClassLoaderEvents(): void;
  }
}
