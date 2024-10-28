<?php

namespace Websyspro\NestPhp\Lib\Entitys\Base\Controllers
{
  use Websyspro\NestPhp\Lib\Dataset\Repository;
  use Websyspro\NestPhp\Lib\Entitys\Base\ProductEntity;
  use Websyspro\NestPhp\Lib\Entitys\Base\ProductGroupEntity;
  use Websyspro\NestPhp\Lib\Routings\Decorations\Body;
  use Websyspro\NestPhp\Lib\Routings\Decorations\HttpGet;
  use Websyspro\NestPhp\Lib\Routings\Decorations\Middlewares\Controller;

  #[Controller( "product" )]
  class ProductController
  {
    #[HttpGet()]
    public function productList(
      #[Body("page")] string $page
    ): array {
      return Repository::entity(
        ProductEntity::class
      )->includes(
        [
          Repository::entity(
            ProductGroupEntity::class
          )->select(
            [
              "id",
              "description"
            ]
          )
        ]
      )->select(
        [
          "id",
          "description",
          "amount",
          "groupId",
          "quantityInStock",
          "amountInStockTotal",
        ]
      )->paged( (int)$page, 12 )->all();
    }
  }
}
