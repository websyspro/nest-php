<?php

namespace Websyspro\NestPhp\Lib\Database
{
  use Exception;
    use Websyspro\NestPhp\Lib\Logger\Message;

  class DB
  {
    private object $handle;
    private mixed $record;
    private string $error = "";

    public function __construct(
      private string | array $commandSql,
    ){
      $this->report();
      $this->connect();
      $this->execute();
    }

    private function report(
    ): void {
      mysqli_report(
        flags: MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT
      );
    }

    private function envs(
    ): array {
      parse_str(APP_ENVS, $envs);
      return $envs;
    }

    private function connect(
    ): void {
      try{
        $this->handle = mysqli_connect(
          hostname: $this->envs()["host"],
          username: $this->envs()["user"],
          password: $this->envs()["pass"],
          database: $this->envs()["name"],
          port: $this->envs()["port"]
        );
      } catch( Exception $error ) {
        $this->error = mysqli_connect_error();
      }
    }

    public function hasError(
    ): bool {
      return empty($this->error) === false;
    }
    
    public function getError(
    ): string {
      return $this->error;
    }

    public function execute(
    ): void {
      if ($this->hasError() === false){
        try {
          if (is_array($this->commandSql)) {
            if(sizeof($this->commandSql) !== 0) {
              $this->record = mysqli_multi_query(
                $this->handle, implode(";", $this->commandSql)
              );
            }
          } else {
            $this->record = mysqli_query(
              $this->handle, $this->commandSql
            );
          }
        } catch (Exception $error ) {
          $this->error = mysqli_error(
            $this->handle
          );
        }
      }
    }

    public function getRows(
    ): array {
      if ($this->record === false) {
        return [];
      }

      if ( mysqli_num_rows( $this->record ) === 0) {
        return [];
      }

      return mysqli_fetch_all(
        $this->record, MYSQLI_ASSOC
      );
    }

    public function getRow(
    ): array {
      if ($this->record === false) {
        return [];
      }

      if ( mysqli_num_rows( $this->record ) === 0) {
        return [];
      }

      return mysqli_fetch_array(
        $this->record, MYSQLI_ASSOC
      );
    }

    public static function query(
      string | array $commandSql
    ): DB {
      return new static(
        $commandSql
      );
    }

    public function getLastId(): int {
      return mysqli_insert_id(
        $this->handle
      );
    }
  }
}
