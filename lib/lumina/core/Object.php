<?php

namespace lumina\core;

use lumina\core\FactoryException;

class Object{
	
	private static $instances = array();
	
	/**
	 * creates instances of children from the caller class.
	 * i.e.: $myChild = MyClass::factory("MyChildClass");
	 * the example is instance of MyClass or an exception will be thrown.
	 * @param string $className
	 * @throws FactoryException
	 */
	public static function factory($className, $param1 = null, $_ = null){
		$instance = get_called_class();
		$params = func_get_args();
		$className = array_shift($params);
		
		if(!class_exists($className)){
			throw new ObjectException("The class $className does not exists!");
		}
		
		$rc = new \ReflectionClass($className);
		$classInstance = $rc->newInstanceArgs($params);
		if(!($classInstance instanceof $instance)){
			throw new ObjectException("The class $className is not instance of $instance.");
		}
		return $classInstance;
	}
	
	/**
	 * Creates/Loads a singleton instance.
	 * TODO what about constructor parameters? invoking or other ideas?
	 */
	public static function singleton(){
		$instance = get_called_class();
		$params = func_get_args();
		if(!isset(self::$instances[$instance])){
			$rc = new \ReflectionClass($instance);
			self::$instances[$instance] = $rc->newInstanceArgs($params);
		}else{
			// invokes the class. Can be useful to reset/renew arguments.
			$invoker = self::$instances[$instance];
			if(method_exists($invoker, '__invoke')){
				$rm = new \ReflectionMethod($invoker, '__invoke');
				$rm->invokeArgs($invoker, $params);
			}
		}
		return self::$instances[$instance];
	}
	
	public static function hasInstance(){
		$instance = get_called_class();
		return isset(self::$instances[$instance]);
	}
	
	/**
	 * creates a new instance of a child object.
	 */
	final public static function create(){
		$params = func_get_args();
		$className = get_called_class();
		$rc = new \ReflectionClass($className);
		return $rc->newInstanceArgs($params);
	}
	
	/**
	 * returns the instance classname
	 */
	final public static function className(){
		return get_called_class();
	}
	
}