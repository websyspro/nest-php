<?php

namespace Websyspro\NestPhp\Lib\Reflections
{
	class ClassInstance
	{
		public function __construct(
			public object | string $class
		){}

		public function new(): mixed {
			return call_user_func_array( [
				ReflectUtils::getReflectClass(
					$this->class
				), "newInstance"
			], []);
		}
	}
}
