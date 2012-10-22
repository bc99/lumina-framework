<?php

namespace lumina\forks;

/**
 * The old name of this Class was Thread, but it isn't a thread. It is a system process on
 * Linux systems. So I gave it the name "Fork".
 * 
 * The Fork-Class is more Java-like and uses the interface Runnable.
 * There are two ways to implement Classes to the Fork:
 * 1. create an object with Runnable-Interface implemented and add it to the Fork-Constructor
 * 2. create a new class extending the Fork-Class and overwrite the run()-method
 * 
 * @version 1.0.0 - stable
 * 
 * Original by
 * @author Tudor Barbu <miau@motane.lu>
 *
 * Modified and renamed by
 * @author Mario Stöcklein <phoenix4402@gmail.com>
 *
 * @copyright MIT
 */
class Fork implements Runnable{
	
	private static $forks = array();
	
	protected $runnable = null;
	private $pid = null;
	
	/**
	 * Check for children lifetime and remove children which
	 * are finished. If all children are finished, this method
	 * will be finish, too.
	 */
	public static function waitForProcess(){
		$instance = get_called_class();
		if(isset(self::$forks[$instance]) && !empty(self::$forks[$instance])){
			reset(self::$forks[$instance]);
			do{
				$pid = key(self::$forks[$instance]);
				$fork = current(self::$forks[$instance]);
				if(!$fork->isAlive()) unset(self::$forks[$instance][$pid]);
				if(next(self::$forks[$instance]) === false) reset(self::$forks[$instance]);
			}while(!empty(self::$forks[$instance]));
		}
	}
	
	public function __construct(Runnable $runnable = null){
		if(!is_null($runnable)){
			$this->runnable = $runnable;
		}
		else{
			$this->runnable = $this;
		}
	}
	
	/**
	 * current process id.
	 */
	public function getPid(){
		return $this->pid;
	}
	
	/**
	 * compare current process id with a child process id.
	 * @return boolean
	 */
	public function isAlive(){
		$pid = pcntl_waitpid($this->pid, $status, WNOHANG);
		return $pid === 0;
	}
	
	/**
	 * create a child process and save the current fork
	 * into a list of the parent process.
	 */
	public function start(){
		$pid = @pcntl_fork();
		if($pid == -1){
			throw new Exception("no fork exists.");
		}
		if($pid){
			$this->pid = $pid;
			$instance = get_called_class();
			if(!isset(self::$forks[$instance]))
				self::$forks[$instance] = array();
			self::$forks[$instance][$pid] = $this;
		}
		else{
			$this->pid = posix_getpid();
			$this->runnable->run();
			exit(0);
		}
	}
	
	/**
	 * @return boolean
	 */
	public function dispatch(){
		return pcntl_signal_dispatch();
	}
	
	/**
	 * Overwrite this method to create an individual fork.
	 * Don't start the run()-method in your code. Use the
	 * start()-method to start a fork.
	 */
	public function run(){}
	
}
