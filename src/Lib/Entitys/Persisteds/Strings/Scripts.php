<?php

namespace Websyspro\NestPhp\Lib\Entitys\Persisteds\Strings
{
  use Websyspro\NestPhp\Lib\Commons\Utils;

  class Scripts
  {
    public static function scriptEntitys(
      string $database
    ): string {
      return Utils::join( [
        "select information_schema.tables.table_name as entity
           from information_schema.tables
         where information_schema.tables.table_schema='{$database}'"
      ]);
    }

    public static function scriptConstraints(
      string $database,
      string $entity
    ): string {
      return Utils::join( [
        "select information_schema.statistics.index_name as constraint_name
               ,information_schema.statistics.column_name as property
           from information_schema.statistics
          where information_schema.statistics.table_schema = '{$database}'
            and information_schema.statistics.table_name = '{$entity}'
          and ( information_schema.statistics.index_name like 'Unq_%'
             or information_schema.statistics.index_name like 'Idx_%' )
       order by information_schema.statistics.index_name asc
               ,information_schema.statistics.seq_in_index asc"
      ]);
    }

    public static function scriptProperties(
      string $database
    ): string {
      return Utils::join( [
        "select information_schema.columns.table_name as entity
               ,information_schema.columns.column_name as name
               ,information_schema.columns.column_type as type
           ,if( information_schema.columns.is_nullable = 'NO', 1, 0 ) as required
           ,if( information_schema.columns.column_key = 'PRI', 1, 0 ) as primarykey
           ,if( information_schema.columns.extra = 'auto_increment', 1, 0 ) as auto_inc
           from information_schema.columns
          where information_schema.columns.table_schema='{$database}'
       order by information_schema.columns.ordinal_position asc"
      ]);
    }

    public static function scriptForeigns(
      string $database
    ): string {
      return Utils::join( [
        "select referential_constraints.constraint_name as foreign_name
           from information_schema.referential_constraints
           join information_schema.key_column_usage ON referential_constraints.constraint_schema = key_column_usage.table_schema
            and referential_constraints.table_name = key_column_usage.table_name
            and referential_constraints.constraint_name = key_column_usage.constraint_name
          where referential_constraints.constraint_schema = '{$database}'"
      ]);
    }
  }
}
