<?php

namespace Websyspro\NestPhp\Lib\Routings
{
  use Exception;
  use Websyspro\NestPhp\Lib\Commons\Utils;
    use Websyspro\NestPhp\Lib\Data\DataLoad;
    use Websyspro\NestPhp\Lib\Reflections\ReflectUtils;
  use Websyspro\NestPhp\Lib\Routings\Commons\RoutingsUtils;
  use Websyspro\NestPhp\Lib\Routings\Enums\ResponseCode;
  use Websyspro\NestPhp\Lib\Routings\Exceptions\BadRequest;
  use Websyspro\NestPhp\Lib\Routings\Exceptions\NotFound;
    use Websyspro\NestPhp\Lib\Routings\Exceptions\UnAuthorized;

  class Controllers
  {
    private ControllerDetails $controllerDetails;
    private ControllerMethodsDetails $controllerMethodsDetails;

    public function __construct(
      private array $controllers,
      private Response $response
    ){}

    public function getController(
      string $name
    ): array {
      return Utils::filter(
        $this->controllers,
        fn( ControllerDetails $controllerDetails ) => (
          $controllerDetails->getController() === $name
        )
      );
    }

    public function count(): int {
      return sizeof( $this->controllers );
    }

    public function currentController(
    ) : ControllerDetails | null {
      return isset( $this->controllerDetails ) === true
        ? $this->controllerDetails : Utils::shitArray(
          Utils::filter(
            $this->controllers,
            fn( ControllerDetails $controllerDetails ) => (
              $controllerDetails->name === RoutingsUtils::requestUri()->controller
            )
          )
      );
    }

    public function comparePaths(
      array $paths1,
      array $paths2
    ): bool {
      return in_array(
        false,
          Utils::mapper(
          $paths1,
          fn( string $path, int $key ) => (
            $path === $paths2[$key] || preg_match(
              "/^:/", $path
            )
          )
        )
      ) === false;
    }

    public function currentRouterFromController(
    ) : ControllerMethodsDetails | null {
      return isset( $this->controllerMethodsDetails ) === true ? $this->controllerMethodsDetails : Utils::shitArray(
        Utils::filter(
          $this->currentController()->methodsArr,
          fn( ControllerMethodsDetails $controllerMethodsDetails ) => (
            $controllerMethodsDetails->routeMethod === RoutingsUtils::requestUri()->method &&
            $controllerMethodsDetails->pathsCount === RoutingsUtils::requestUri()->pathsCount && (
              $this->comparePaths(
                $controllerMethodsDetails->paths,
                RoutingsUtils::requestUri()->paths
              )
            )
          )
        )
      );
    }

    private function controllerExists(
    ): bool {
      return $this->currentController() !== null
          && $this->currentRouterFromController() !== null;
    }

    private function controllerRequestParams(
    ): void {
      Utils::mapper(
        $this->currentRouterFromController()->paths,
        function( string $value, string $key ) {
          if ( preg_match( "/^:/", $value ) ) {
            DataLoad::$data["PARAM"][
              preg_replace( "/^:/", "", $value )
            ] = RoutingsUtils::requestUri()->paths[$key];
          }
        }
      );
    }

    private function controllerExecuteMiddlewares(
    ): void {
      Utils::mapper(
        $this->currentController()->middlewaresArr,
        fn( mixed $middleware ) => (
          $middleware->execute(
            $this->response
          )
        )
      );
    }

    private function controllerRoutersExecuteMiddlewares(
    ): void {
      Utils::mapper(
        $this->currentRouterFromController()->middlewaresArr,
        fn( mixed $middleware ) => (
          $middleware->execute(
            $this->response
          )
        )
      );
    }

    private function getPropertiesFromConstructor(
    ): array {
      $hasConstruct = Utils::filter(
        $this->currentController()->methodsArr,
        fn( ControllerMethodsDetails $controllerMethodsDetails ) => (
          $controllerMethodsDetails->method === "__construct"
        )
      );

      if ( empty( $hasConstruct )) {
        return [];
      }

      return Utils::shitArray(
        $hasConstruct
      )->properties;
    }

    private function controllerRoutersExecute(
    ): void {
      try {
        $handleClass = call_user_func_array( [
          ReflectUtils::getReflectClass(
            $this->currentRouterFromController()->base
          ), "newInstance"
        ], Utils::mapper(
          $this->getPropertiesFromConstructor(),
          fn( mixed $property ) => new $property()
        ));

        $this->response->send(
          call_user_func_array(
            [ $handleClass, $this->currentRouterFromController()->method ],
            Utils::mapper(
              $this->currentRouterFromController()->properties,
              fn( mixed $property ) => $property->execute()
            )
          )
        );
      } catch ( Exception $exception ){
        $this->exceptionTypes(
          $exception
        );
      }
    }

    public function getRequestUri(): string {
      return RoutingsUtils::getRequestUri();
    }

    public function getRequestUriMethod(
    ): string {
      return RoutingsUtils::requestUri()->method;
    }

    public function search(
    ) : void {
      try {
        if ( RoutingsUtils::requestUri()->isValid === true ) {
          if ( $this->controllerExists() === true ) {
            $this->controllerRequestParams();
            $this->controllerExecuteMiddlewares();
            $this->controllerRoutersExecuteMiddlewares();
            $this->controllerRoutersExecute();
          } else {
            Error::notFound(
              "O endpoint {$this->getRequestUriMethod()} '{$this->getRequestUri()}' não foi encontrado neste servidor."
            );
          }
        } else {
          $this->response->send(
            "O servidor está em execução"
          );
        }
      } catch( Exception $exception ) {
        $this->exceptionTypes(
          $exception
        );
      }
    }

    private function exceptionTypes(
      Exception $exception
    ): void {
      if ( $exception::class === BadRequest::class ) {
        $this->response->send( $exception->getMessage(), false, ResponseCode::badRequest );
      } elseif( $exception::class === NotFound::class ) {
        $this->response->send( $exception->getMessage(), false, ResponseCode::notFound );
      } elseif( $exception::class === UnAuthorized::class ) {
        $this->response->send( $exception->getMessage(), false, ResponseCode::unauthorized );
      } else {
        $this->response->send( $exception->getMessage(), false, ResponseCode::badRequest );
      }
    }
  }
}
