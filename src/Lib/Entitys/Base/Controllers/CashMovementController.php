<?php

namespace Websyspro\NestPhp\Lib\Entitys\Base\Controllers
{
  use Websyspro\NestPhp\Lib\Dataset\Repository;
  use Websyspro\NestPhp\Lib\Entitys\Base\CashierEntity;
  use Websyspro\NestPhp\Lib\Entitys\Base\CashMovementEntity;
  use Websyspro\NestPhp\Lib\Routings\Decorations\HttpGet;
  use Websyspro\NestPhp\Lib\Routings\Decorations\Middlewares\Controller;

  #[Controller("cash-movement")]
  class CashMovementController
  {
    #[HttpGet("summary-of-the-day")]
    public function summaryOfTheDay(
    ): array {
      return Repository::entity(
        CashierEntity::class
      )->select(
        [
          "id",
          "description"
        ]
      )->find(
        [
          "id" => "1;2;3;4;5;6"
        ]
      )->includes(
        [
          Repository::entity(
            CashMovementEntity::class
          )->select(
            [
              "paymentMethod",
              "sum(amount)" => "amount",
              "count(amount)" => "count"
            ]
          )->find(
            [
              "dateMovement" => "between '2023-04-03 00:00:01' and '2023-04-03 23:59:59'",
              "documentId" => "not in (111111,888888,999999)",
              "descriptionMovement" => "like 'Recebimento de Cartão%'"
            ]
          )->groupby(
            [
              "cashierId",
              "paymentMethod"
            ]
          )->orderBy(
            [
              "cashierId" => "asc",
              "paymentMethod" => "asc"
            ]
          )->includes(
            [
              
            ]
          )
        ]
      )->all();
    }
  }
}
