<?php

namespace Websyspro\NestPhp\Lib\Dataset
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  use Websyspro\NestPhp\Lib\Dataset\Persisted\Create;
  use Websyspro\NestPhp\Lib\Dataset\Persisted\Delete;
  use Websyspro\NestPhp\Lib\Dataset\Persisted\Update;
  use Websyspro\NestPhp\Lib\Dataset\Sqls\Enums\FindType;
  use Websyspro\NestPhp\Lib\Dataset\Sqls\FindList;
  use Websyspro\NestPhp\Lib\Entitys\Designs\DesignList;
  use Websyspro\NestPhp\Lib\Entitys\Enums\PropertyEntity;

  class RepositoryAbstract
  {
    public DesignList $designList;
    public array $selectArr = [];
    public array $findListArr = [];
    public array $referenceArr = [];
    public array $groupbyArr = [];
    public array $orderByArr = [];
    public int $page = -1;
    public int $pageRows = -1;

    public array $includesArr = [];

    public function __construct(
      public string $entity
    ){
      $this->designList = new DesignList(
        $this->entity
      );
    }

    public function envs(
      string $key
    ): string {
      parse_str(APP_ENVS, $envs);
      return $envs[ $key ];
    }

    public static function entity(
      string $entity
    ): Repository {
      return new Repository( $entity );
    }

    public function create(
      array $data = []
    ): int {
      return ( new Create(
        $this->entity,
        $data
      ))->setPersisteds();
    }

    public function update(
      array $data = []
    ): Repository {
      ( new Update(
        $this->entity,
        $data
      ))->setPersisteds();

      return $this;
    }

    public function delete(
      array | string | int $value = []
    ): int {
      return ( new Delete(
        $this->entity,
        is_array( $value )
          ? $value : [ PropertyEntity::Id->value => $value ]
      ))->setPersisteds();
    }

    public function includes(
      array $repositorys
    ): Repository {
      Utils::mapper(
        $repositorys,
        fn( Repository $repository ) => (
          $this->includesArr[
            lcfirst( $repository->getTable() )
          ] = $repository
        )
      );
      
      return $this;
    }

    public function getTable(
    ): string {
      return Utils::entityName(
        $this->entity
      );
    }

    public function createFindList(
      string $entity,
      array $find,
      FindType $findType = FindType::FindAnd
    ): FindList {
      return ( new FindList(
          $entity,
          $find,
          $findType
      ))->parseProperties();
    }

    public function find(
      string | int | array $findArr
    ): Repository {
      if ( is_array( $findArr )) {
        $this->findListArr[] = $this->createFindList(
          $this->entity,
          $findArr,
          FindType::FindAnd
        );
      } elseif ( is_string( $findArr ) || is_numeric( $findArr )) {
        $this->findListArr[] = $this->createFindList(
          $this->entity,
          [ PropertyEntity::Id->value => $findArr ],
          FindType::FindAnd
        );
      }

      return $this;
    }

    public function findOr(
      string | int | array $find
    ): Repository {
      $this->findListArr[] = $this->createFindList(
        $this->entity,
        $find,
        FindType::FindOr
      );

      return $this;
    }

    public function findAnd(
      string | int | array $find
    ): Repository {
      $this->findListArr[] = $this->createFindList(
        $this->entity,
        $find,
        FindType::FindAnd
      );

      return $this;
    }

    public function reference(
      string | int | array $find
    ): Repository {
      $this->referenceArr[] = $this->createFindList(
        $this->entity,
        $find,
        FindType::FindAnd
      );

      return $this;
    }

    public function groupby(
      array $groupbyArr
    ): Repository {
      $this->groupbyArr = $groupbyArr;
      return $this;
    }

    public function select(
      array $selectArr = []
    ): Repository {
      $this->selectArr = $selectArr;
      return $this;
    }

    public function orderBy(
      array $orderByArr = []
    ): Repository {
      $this->orderByArr = $orderByArr;
      return $this;
    }

    public function paged(
      int $page,
      int $pageRows = 12
    ): Repository {
      $this->page = $page;
      $this->pageRows = $pageRows;
      return $this;
    }
  }
}
