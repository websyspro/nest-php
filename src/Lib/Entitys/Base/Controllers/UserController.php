<?php

namespace Websyspro\NestPhp\Lib\Entitys\Base\Controllers
{
  use Websyspro\NestPhp\Lib\Dataset\Repository;
  use Websyspro\NestPhp\Lib\Entitys\Base\ClientEntity;
  use Websyspro\NestPhp\Lib\Entitys\Base\DocumentEntity;
  use Websyspro\NestPhp\Lib\Entitys\Base\DocumentItemEntity;
  use Websyspro\NestPhp\Lib\Entitys\Base\ProductEntity;
  use Websyspro\NestPhp\Lib\Routings\Decorations\Body;
  use Websyspro\NestPhp\Lib\Routings\Decorations\HttpGet;
  use Websyspro\NestPhp\Lib\Routings\Decorations\HttpPut;
  use Websyspro\NestPhp\Lib\Routings\Decorations\Middlewares\Authenticate;
  use Websyspro\NestPhp\Lib\Routings\Decorations\Middlewares\Controller;
  use Websyspro\NestPhp\Lib\Routings\Decorations\Middlewares\FileValidate;
  use Websyspro\NestPhp\Lib\Routings\Error;
  
  #[Controller( "user" )]
  #[Authenticate()]
  class UserController
  {
    public function __construct(
      private TestService $testService
    ){}

    #[HttpGet( "email/:email" )]
    public function listTest(
    ): array {
      return Repository::entity(
        ClientEntity::class
      )->select(
        [ "id", "cpf", "description", "createdAt" ]
      )->orderBy(
        [ "id" => "DESC"]
      )->paged(
        1, 4
      )->includes(
        [
          Repository::entity(
            DocumentEntity::class
          )->select(
            [
              "id",
              "type",
              "cashierId",
              "operatorId",
              "status",
              "totalPayable",
              "createdAt"
            ]
          )->includes(
            [
              Repository::entity(
                DocumentItemEntity::class
              )->select(
                [
                  "id",
                  "productId",
                  "productAmount",
                  "productQuantity",
                  "productDiscount",
                  "productAmountTotal",
                ]
              )->includes(
                [
                  Repository::entity(
                    ProductEntity::class
                  )->select(
                    [
                      "id",
                      "description",
                    ]
                  )
                ]
              )
            ]
          )
        ]
      )->all();
    }

    #[HttpPut( "getitem/:email/:id" )]
    #[FileValidate( [ "jpg", "png" ], 1024 )]
    public function getEmail(
      #[Body( "propostId" )] int $propostId,
      #[Body( "clientId" )] array $clientId
    ): array {
      return [
        $propostId,
        $clientId
      ];
    }
  }
}
