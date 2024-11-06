<?php

namespace Websyspro\NestPhp\Lib\Dataset
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  use Websyspro\NestPhp\Lib\Database\DB;
  use Websyspro\NestPhp\Lib\Dataset\Persisted\Commons\PersistedUtils;
  use Websyspro\NestPhp\Lib\Dataset\Sqls\Enums\FindType;
  use Websyspro\NestPhp\Lib\Dataset\Sqls\FindList;
  use Websyspro\NestPhp\Lib\Entitys\Abstract\EntityForeign;
  use Websyspro\NestPhp\Lib\Entitys\Enums\PropertyEntity;

  class Repository extends RepositoryAbstract
  {
    private function getSelectsCount(
    ): string {
      return "select count(*) as countRows";
    }

    private function getSelects(
    ): string {
      if ( Utils::isEmptyArray( $this->selectArr )) {
        return "select *";
      }

      return sprintf(
        "select %s",
        Utils::join(
          Utils::mapper(
            $this->selectArr,
            function( string $property, string $key ) {
              if ( is_numeric( $key )) {
                return "{$this->getTable()}.{$property} as {$property}";
              }

              if ( $this->designList->properties->isProperty( $key ) === true) {
                return "{$this->getTable()}.{$key} as {$property}";
              } else {
                return "{$key} as {$property}";
              }
            }
          ), ", "
        )
      );
    }

    private function getFroms(
    ): string {
      return sprintf(
        "from %s.%s",
        $this->envs("name"), Utils::entityName(
          $this->entity
        )
      );
    }

    private function getFinds(
    ): string | null {
      $finds = Utils::mapper(
        Utils::arrayMerge(
          Utils::arrayMerge(
            [
              $this->createFindList(
                $this->entity,
                [
                  PropertyEntity::Actived->value => 1,
                  PropertyEntity::Deleted->value => 0
                ],
                FindType::FindAnd
              )
            ],
            $this->findListArr
          ),
          $this->referenceArr,
        ),
        fn( FindList $findList, string $order ) => (
          $findList->getPropertiesGroup($order)
        )
      );

      if ( Utils::isEmptyArray( $finds )) {
        return "where 1=1";
      }

      return sprintf(
        "where %s",
        Utils::join(
          $finds,
          " "
        )
      );
    }

    private function getGroupBys(
    ): string | null {
      if ( Utils::isEmptyArray( $this->groupbyArr )) {
        return null;
      }

      return sprintf(
        "group by %s",
        Utils::join(
          Utils::mapper(
            $this->groupbyArr,
            fn( string $property ) => (
              sprintf( "%s.%s", Utils::entityName( $this->entity ), $property )
            )
          ), ", "
        )
      );
    }

    private function getOrderBys(
    ): string | null {
      if ( Utils::isEmptyArray( $this->orderByArr )) {
        if ( $this->designList->properties->isProperty( PropertyEntity::CreatedAt->value )  ) {
          return sprintf(
            "order by %s.%s desc",
            $this->getTable(), PropertyEntity::CreatedAt->value
          );
        }

        return "order by 1 asc";
      }

      return sprintf(
        "order by %s",
        Utils::join(
          Utils::mapper(
            $this->orderByArr,
            fn( string $order, string $by ) => (
              sprintf( "%s %s", $by, $order )
            )
          ), ", "
        )
      );
    }

    private function getPageds(
    ): string | null {
      if ( $this->page === -1 && $this->pageRows === -1 ) {
        return null;
      }

      if ( $this->page === 0 || $this->pageRows === 0 ) {
        return sprintf(
          "limit %s, %s",
          $this->page, $this->pageRows
        );
      }

      return sprintf(
        "limit %s, %s",
        ( $this->page - 1 ) * $this->pageRows, $this->pageRows
      );
    }

    private function getSqlCount(
    ): string {
      return Utils::join(
        [
          $this->getSelectsCount(),
          $this->getFroms(),
          $this->getFinds(),
          $this->getGroupBys()
        ],
        " "
      );
    }

    private function getSql(
    ): string {
      return Utils::join(
        [
          $this->getSelects(),
          $this->getFroms(),
          $this->getFinds(),
          $this->getGroupBys(),
          $this->getOrderBys(),
          $this->getPageds()
        ],
        " "
      );
    }

    public function parseRow(
      array $row = []
    ): array {
      return Utils::mapper(
        $row,
        fn( mixed $value, string $property ) => (
          is_null($value) ? $value : PersistedUtils::decodeType(
            $value,
            $this->designList->properties->type(
              $property, true
            )
          )
        )
      );
    }

    private function hasMany(
      Repository $repository
    ): array {
      return Utils::filter(
        $repository->designList->foreigns->items,
        fn( EntityForeign $entityForeign ) => (
          $this->designList->properties->isProperty(
            $entityForeign->referenceKey
          ) && $entityForeign->reference === $this->getTable()
        )
      );
    }

    private function hasOne(
      Repository $repository
    ): array {
      return Utils::filter(
        $this->designList->foreigns->items,
        fn( EntityForeign $entityForeign ) => (
          $repository->designList->properties->isProperty(
            $entityForeign->referenceKey
          ) && $entityForeign->reference === $repository->getTable()
        )
      );
    }

    private function getIncludeFindArr(
      array $entityForeign,
      array $row,
       bool $hasOne
    ): array {
      return Utils::shitArray(
        Utils::mapper(
          $entityForeign,
          fn( EntityForeign $entityForeign ) => (
            [ $hasOne ? $entityForeign->key : $entityForeign->referenceKey => preg_replace(
              [ "/^\'/", "/'$/"],
              "",
              PersistedUtils::encodeType(
                $row[ $hasOne ? $entityForeign->referenceKey : $entityForeign->key ],
                $this->designList->properties->type(
                  $hasOne ? $entityForeign->referenceKey : $entityForeign->key
                )
              )
            )]
          )
        )
      );
    }

    private function getIncludeFind(
      Repository $repository,
      array $row
    ): array {
      $hasMany = $this->hasMany( $repository );
      $hasOne = $this->hasOne( $repository );

      if ( Utils::isEmptyArray( $hasMany ) === false ) {
        return $this->getIncludeFindArr( $hasMany, $row, true);
      }

      if ( Utils::isEmptyArray( $hasOne ) === false ) {
        return $this->getIncludeFindArr( $hasOne, $row, false);
      }

      return [];
    }

    public function clearReference(
      Repository $repository
    ): Repository {
      $this->referenceArr = [];
      return $repository;
    }

    public function getIncludes(
      array $row
    ): array {
      if ( Utils::isEmptyArray( $this->includesArr )) {
        return [];
      }

      return Utils::mapper(
        $this->includesArr,
        fn( Repository $repository ) => (
          $this->hasMany( $repository)
            ? $repository->reference(
                $this->getIncludeFind(
                  $repository->clearReference(
                    $repository
                  ), $row
                )
              )->all()
            : $repository->reference(
                $this->getIncludeFind(
                  $repository->clearReference(
                    $repository
                  ), $row
                )
              )->one()
        )
      );
    }
    
    public function Count(
    ): int {
      $recodset = DB::query(
        $this->getSqlCount()
      );

      if ( $recodset->hasError() ){
        return 0;
      }

      return $recodset->getRow()[
        "countRows"
      ];
    }

    public function Exist(
    ): bool {
      return static::Count() !== 0;
    }

    public function all(
    ): array {
      $recodset = DB::query(
        $this->getSql()
      );

      if ( $recodset->hasError() ){
        return [];
      }

      return Utils::mapper(
        $recodset->getRows(),
        fn( array $row ) => (
          Utils::arrayMerge(
            $this->parseRow( $row ),
            $this->getIncludes( $row )
          )
        )
      );
    }

    public function one(
    ): array {
      $recodset = DB::query(
        $this->getSql()
      );

      if ( $recodset->hasError() ){
        return [];
      }

      return Utils::shitArray(
        Utils::mapper(
          [ $recodset->getRow() ],
          fn( array $row ) => (
            Utils::arrayMerge(
              $this->parseRow( $row ),
              $this->getIncludes( $row )
            )
          )
        )
      );
    }
  }
}
