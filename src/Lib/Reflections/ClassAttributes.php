<?php

namespace Websyspro\NestPhp\Lib\Reflections
{
	class ClassAttributes
	{
		public function __construct(
			public object | string $objectOrClass,
			public array $objectOrClassArgs = []
		){}

		public function new(): mixed {
			return call_user_func_array( [
				ReflectUtils::getReflectClass( $this->objectOrClass), "newInstance"
			], $this->objectOrClassArgs );
		}
	}
}

