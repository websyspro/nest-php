<?php

namespace Websyspro\NestPhp\Lib\Dataset\Sqls
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  use Websyspro\NestPhp\Lib\Dataset\Sqls\Enums\FindType;
  use Websyspro\NestPhp\Lib\Entitys\Designs\DesignList;
  use Websyspro\NestPhp\Lib\Dataset\Persisted\Commons\PersistedUtils;
    use Websyspro\NestPhp\Lib\Entitys\Enums\EntityType;

  class AbstractFindList
  {
    public DesignList $designList;
    public array $properties = [];

    public function __construct(
      string $entity,
      private array $find,
      public FindType $findType = FindType::FindAnd
    ){
      $this->designList = new DesignList(
        $entity
      );
    }

    public function parseProperties(
    ): AbstractFindList {
      $this->properties = Utils::mapper(
        $this->find,
        fn( string $value, string $property ) =>
          $this->parsePropertiesEncode(
            $property,
            $value,
            $this->designList->properties->types()[$property]
          )
      );

      return $this;
    }

    private function parsePropertiesEncodeIsDatetime(
      string $indexValue,
      string $type,
      int $order
    ): string {
      if ( $type === EntityType::Datetime->value ) {
        return $order === 1
          ? "{$indexValue} 00:00:01"
          : "{$indexValue} 23:59:59";
      }

      return $indexValue;
    }

    private function checkIsLikeds(
      string $value
    ): string {
      return preg_match( "/\*/", $value)
        ? str_replace( "/\*/", "%", $value )
        : "{$value}%";
    }

    private function parsePropertiesEncode(
      string $property,
      string $value,
      string $type
    ): string {
      if ( preg_match(
        "/(^is)|(^not in)|(^in)|(^between)|(^like)|(^=)|(^<)|(^>)/",
        trim(
          $value
        ))
      ){
        return "{$property} {$value}";
      }

      if ( preg_match("/\|/", $value ) || preg_match("/\;/", $value ) ) {
        if ( preg_match("/\|/", $value )) {
          $findsArr = Utils::join(
            Utils::arrayMerge(
              [ utils::join(
                [ $this->designList->entity, $property ],
                "."
              ), "between" ], [
                Utils::join(
                  Utils::mapper(
                    explode( "|", $value ),
                    fn( string $indexValue, int $order ) => PersistedUtils::encodeType(
                      $this->parsePropertiesEncodeIsDatetime(
                        $indexValue,
                        $type,
                        $order
                      ), $type
                    )
                  ), " and "
                )
              ]
            ), " "
          );
        }

        if ( preg_match("/\;/", $value )) {
          $findsArr = Utils::join(
            Utils::arrayMerge(
              [ utils::join(
                [ $this->designList->entity, $property ],
                "."
              ), "in" ], [
                Utils::betweenRelatives(
                  Utils::join(
                    Utils::mapper(
                      explode( ";", $value ),
                      fn( string $indexValue ) => PersistedUtils::encodeType(
                        $indexValue, $type
                      )
                    ), ","
                  )
                )
              ]
            ), " "
          );
        }

        return $findsArr;
      }

      return Utils::join(
        Utils::arrayMerge(
          [ utils::join( [ $this->designList->entity, $property ], "." ) ],
          [
            $type !== EntityType::Varchar->value
              ? PersistedUtils::encodeType( $value, $type )
              : PersistedUtils::encodeType(
                 $this->checkIsLikeds( $value), $type
              )
          ]
        ),  $type !== EntityType::Varchar->value ? " = " : " like "
      );
    }
  }
}
