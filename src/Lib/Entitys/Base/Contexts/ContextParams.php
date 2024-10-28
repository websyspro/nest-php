<?php

namespace Websyspro\NestPhp\Lib\Entitys\Base\Contexts
{
  class ContextParams
  {
    public function __construct(
      private object $params
    ){}

    public function find(
    ): array {
      $find = [];

      if ( isset( $this->params->find ) === false ){
        return [];
      }

      foreach( $this->params->find as $field => $findText) {
        $find[$field] = $findText;
      }

      return $find;
    }

    public function orderBy(
    ): array {
      $orderBy = [];

      if ( isset( $this->params->orderBy ) === false ){
        return [];
      }

      foreach( $this->params->orderBy as $order => $by) {
        $orderBy[$order] = $by;
      }

      return $orderBy;
    }

    public function page(
    ): int {
      return isset( $this->params->page )
        ? $this->params->page
        : 1;
    }

    public function pageRows(
    ): int {
      return isset( $this->params->pageRows )
        ? $this->params->pageRows
        : 6;
    }
  }
}
