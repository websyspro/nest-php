<?php

namespace Websyspro\NestPhp\Lib\Reflections
{
	use ReflectionAttribute;
	use ReflectionClass;
	use ReflectionProperty;
	use Websyspro\NestPhp\Lib\Commons\Utils;

	class ClassLoader
	{
		private ReflectionClass $reflectionClass;
		public array $middlewares = [];
		public array $properties = [];
		public array $methods = [];

		public function __construct(
			private readonly string | object $objectOrClass
		){
			$this->setReflectClass();
			$this->setReflectMiddlewares();
			$this->setReflectPropertys();
			$this->setReflectMethods();
		}

		private function setReflectClass(
		): void {
			$this->reflectionClass = ReflectUtils::getReflectClass(
				$this->objectOrClass
			);
		}

		private function setReflectMiddlewares(
		): void {
			Utils::mapper( 
				$this->reflectionClass->getAttributes(),
				fn( ReflectionAttribute $reflectionAttribute) => (
					$this->middlewares[] = ReflectUtils::getClassAttribte(
						$reflectionAttribute
					)->new()
				)
			);
		}

		private function setReflectPropertys(
		): void {
			Utils::mapper(
				$this->reflectionClass->getProperties(),
				fn( ReflectionProperty $reflectionProperty) => (
					Utils::mapper(
						$reflectionProperty->getAttributes(),
						fn( ReflectionAttribute $reflectionAttribute ) => (
							isset($this->properties[ $reflectionProperty->getName() ]) === false
								? $this->properties[ $reflectionProperty->getName() ] = ReflectUtils::getClassAttribte(
										$reflectionAttribute
									)->new()->execute()
								: $this->properties[ $reflectionProperty->getName() ] = array_merge(
									$this->properties[ $reflectionProperty->getName() ], ReflectUtils::getClassAttribte(
										$reflectionAttribute
									)->new()->execute()
							)
						)
					)
				)
			);
		}

		private function setReflectMethods(
		): void {
			Utils::mapper(
				ReflectUtils::getMethdos( $this->objectOrClass ),
				fn( string $method ) => (
					$this->methods[ $method ] = new ClassMethods(
						$this->objectOrClass, $method
					)
				)
			);
		}
	}
}
