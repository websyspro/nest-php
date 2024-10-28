<?php

use Websyspro\NestPhp\Examples\Contexts\DocumentContext;
use Websyspro\NestPhp\Examples\Contexts\OperatorContext;
use Websyspro\NestPhp\Examples\Contexts\ProductContext;
use Websyspro\NestPhp\Examples\Controllers\CashMovementController;
use Websyspro\NestPhp\Examples\Controllers\ClientController;
use Websyspro\NestPhp\Examples\Controllers\ProductController;
use Websyspro\NestPhp\Examples\Controllers\UserController;
use Websyspro\NestPhp\Examples\Entitys\CashierEntity;
use Websyspro\NestPhp\Examples\Entitys\CashMovementEntity;
use Websyspro\NestPhp\Examples\Entitys\ClientEntity;
use Websyspro\NestPhp\Examples\Entitys\DocumentEntity;
use Websyspro\NestPhp\Examples\Entitys\DocumentItemEntity;
use Websyspro\NestPhp\Examples\Entitys\OperatorEntity;
use Websyspro\NestPhp\Examples\Entitys\ProductEntity;
use Websyspro\NestPhp\Examples\Entitys\ProductGroupEntity;
use Websyspro\NestPhp\Examples\Entitys\SettingEntity;
use Websyspro\NestPhp\Lib\Server;

Server::create(
  [
    UserController::class,
    ClientController::class,
    CashMovementController::class,
    ProductController::class
  ],
  [
    SettingEntity::class,
    ClientEntity::class,
    OperatorEntity::class,
    DocumentEntity::class,
    DocumentItemEntity::class,
    ProductGroupEntity::class,
    ProductEntity::class,
    CashierEntity::class,
    CashMovementEntity::class
  ],
  [
    ProductContext::class,
    OperatorContext::class,
    DocumentContext::class
  ]
);
