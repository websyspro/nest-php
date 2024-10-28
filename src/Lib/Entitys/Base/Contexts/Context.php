<?php

namespace Websyspro\NestPhp\Lib\Entitys\Base\Contexts
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  use Websyspro\NestPhp\Lib\Dataset\Repository;
  use Websyspro\NestPhp\Lib\Entitys\Base\DefaultEntity;
  use Websyspro\NestPhp\Lib\Routings\Error;
  use Websyspro\NestPhp\Lib\Server;

  class Context implements ContextInterface
  {
    public function repository(
    ): Repository {
      return Repository::entity(
        DefaultEntity::class
      );
    }

    public function get(
      ContextParams $contextParams
    ): array {
      $repositoryWithFind = empty(
        $contextParams->find()) === false
          ? $this->repository()->find( $contextParams->find())
          : $this->repository();

      $rowsCount = $repositoryWithFind->Count();
      $pageCount = (int)ceil(
        bcdiv(
          $rowsCount,
          $contextParams->pageRows(),
          2
        )
      );

      return [
        "pageCount" => $pageCount,
        "page" => $contextParams->page(),
        "pageRows" => $contextParams->pageRows(),
        "rowsCount" => $rowsCount,
        "rows" => $repositoryWithFind->orderBy(
            $contextParams->orderBy()
          )->paged(
            $contextParams->page(),
            $contextParams->pageRows()
          )->all()
      ];
    }

    private function getContexts(
      string $name
    ): array {
      $contexts = Utils::filter(
        Server::$contexts,
        fn( string $context ) => (
          Utils::contextName(
            $context
          ) === $name
        )
      );

      if ( empty( $contexts )) {
        Error::badRequest(
          "Context `{$name}` not found."
        );
      }

      return $contexts;
    }

    public function browser(
      string $name,
      object $params
    ): array {
      return Utils::mapperFirst(
        $this->getContexts( $name ),
        fn( string $context ) => (
          (new $context)->get(
            new ContextParams(
              $params
            )
          )
        )
      );
    }

    public function create(
      string $name,
       array $rows
    ): string {
      return Utils::shitArray(
        Utils::mapperFirst(
          $this->getContexts( $name ),
          fn( string $context ) => (
            Utils::mapper(
              $rows,
              fn( mixed $data ) => (
                ( new $context )->repository()->create(
                  ( array )$data
                )
              )
            )
          )
        )
      );
    }

    public function update(
      string $name,
      array $rows
    ): string {
      return Utils::shitArray(
        Utils::mapperFirst(
          $this->getContexts( $name ),
          fn( string $context ) => (
            Utils::mapper(
              $rows,
              fn( mixed $data ) => (
                ( new $context )->repository()->update(
                  ( array )$data
                )
              )
            )
          )
        )
      );
    }

    public function delete(
      string $name,
      array $rows
    ): string {
      return Utils::shitArray(
        Utils::mapperFirst(
          $this->getContexts( $name ),
          fn( string $context ) => (
            Utils::mapper(
              $rows,
              fn( mixed $data ) => (
                ( new $context )->repository()->delete(
                  ( array )$data
                )
              )
            )
          )
        )
      );
    }
  }
}
