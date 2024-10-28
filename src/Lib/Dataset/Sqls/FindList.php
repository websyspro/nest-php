<?php

namespace Websyspro\NestPhp\Lib\Dataset\Sqls
{
  use Websyspro\NestPhp\Lib\Commons\Utils;

  class FindList extends AbstractFindList
  {
    public function getPropertiesGroup(
      string $order
    ): string {
      return sprintf(
        "%s (%s)",
        (int)$order !== 0 ? $this->findType->value : "", Utils::join(
          $this->properties,
          " and "
        )
      );
    }
  }
}
