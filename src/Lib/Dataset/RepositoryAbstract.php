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
      return new static( $entity );
    }

    public function create(
      array $data = []
    ): string {
      return ( new Create(
        $this->entity,
        $data
      ))->setPersisteds();
    }

    public function update(
      array $data = []
    ): string {
      return ( new Update(
        $this->entity,
        $data
      ))->setPersisteds();
    }

    public function delete(
      array $data = []
    ): string {
      return ( new Delete(
        $this->entity,
        $data
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
      $this->findListArr[] = $this->createFindList(
        $this->entity,
        $findArr,
        FindType::FindAnd
      );

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
