<?php

namespace Websyspro\NestPhp\Lib\Entitys\Abstract
{
  class PersistedList
  {
    public EntityRequiredList $requireds;
    public EntityAutoIncList $autoincs;
    public EntityForeignList $foreigns;
    public EntityConstraintList $constraints;
    public EntityPropertyList $properties;
    public EntityPrimaryKeyList $primarykeys;

    public function __construct(
      public string | object $entity
    ){
      $this->setClassLoaderInit();
    }

    private function setClassLoaderInit(
    ): void {
      $this->foreigns = new EntityForeignList;
      $this->autoincs = new EntityAutoIncList;
      $this->requireds = new EntityRequiredList;
      $this->properties = new EntityPropertyList(
        $this->entity
      );
      $this->primarykeys = new EntityPrimaryKeyList;
      $this->constraints = new EntityConstraintList(
        new EntityConstraint(),
        new EntityConstraint()
      );
    }
  }
}
