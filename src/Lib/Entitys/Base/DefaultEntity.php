<?php

namespace Websyspro\NestPhp\Lib\Entitys\Base
{
  use Websyspro\NestPhp\Lib\Entitys\Columns\AutoInc;
  use Websyspro\NestPhp\Lib\Entitys\Columns\Flag;
  use Websyspro\NestPhp\Lib\Entitys\Columns\Datetime;
  use Websyspro\NestPhp\Lib\Entitys\Columns\Numeric;
  use Websyspro\NestPhp\Lib\Entitys\Constraints\PrimaryKey;
  use Websyspro\NestPhp\Lib\Entitys\Constraints\Required;
  use Websyspro\NestPhp\Lib\Entitys\Defaults\BeforeDefaultCreate;
  use Websyspro\NestPhp\Lib\Entitys\Defaults\BeforeDefaultDelete;
  use Websyspro\NestPhp\Lib\Entitys\Defaults\BeforeDefaultUpdate;
  use Websyspro\NestPhp\Lib\Entitys\Generations\Actived;
  use Websyspro\NestPhp\Lib\Entitys\Generations\AutoDate;
    use Websyspro\NestPhp\Lib\Entitys\Generations\DefaultDeleted;
    use Websyspro\NestPhp\Lib\Entitys\Generations\Deleted;
  use Websyspro\NestPhp\Lib\Entitys\Generations\SessionUserId;

  class DefaultEntity
  {
    #[AutoInc()]
    #[Required()]
    #[PrimaryKey()]
    #[Numeric()]
    public string $id;

    #[Flag()]
    #[Required()]
    #[BeforeDefaultCreate( Actived::class)]
    public string $actived;

    #[Numeric()]
    #[Required()]
    #[BeforeDefaultCreate( SessionUserId::class)]
    public string $activedBy;

    #[Datetime()]
    #[Required()]
    #[BeforeDefaultCreate( AutoDate::class)]
    public string $activedAt;

    #[Numeric()]
    #[Required()]
    #[BeforeDefaultCreate( SessionUserId::class)]
    public string $createdBy;
  
    #[Datetime()]
    #[Required()]
    #[BeforeDefaultCreate( AutoDate::class)]
    public string $createdAt;

    #[Numeric()]
    #[BeforeDefaultUpdate( SessionUserId::class)]
    public string $updatedBy;
  
    #[Datetime()]
    #[BeforeDefaultUpdate( AutoDate::class)]
    public string $updatedAt;
    
    #[Flag()]
    #[BeforeDefaultCreate( DefaultDeleted::class )]
    #[BeforeDefaultDelete( Deleted::class )]
    public int $deleted;

    #[Numeric()]
    #[BeforeDefaultDelete( SessionUserId::class)]
    public string $deletedBy;
    
    #[Datetime()]
    #[BeforeDefaultDelete( AutoDate::class)]
    public string $deletedAt;
  }
}
