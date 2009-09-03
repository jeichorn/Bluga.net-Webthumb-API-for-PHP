<?php

class Bluga_Propertybag implements iterator, ArrayAccess, Countable {

	private $bag = array();

	public $setters = array();

	protected $allowed = array();

	public function __construct($allowed = array(),$setIterationOrdertoAllowed = true) {
		if ($setIterationOrdertoAllowed) {
			foreach($allowed as $key) {
				$this->bag[$key] = null;
			}
		}

		$this->allowed = array_flip($allowed);
	}

	public function __get($key) {
		if (!isset($this->allowed[$key])) {
			throw new Exception("Field: $key isn't Allowed");
		}
		if (isset($this->bag[$key])) {
			return $this->bag[$key];
		}
		return null;
	}

	public function __set($key,$val) {
		if (!isset($this->allowed[$key])) {
			throw new Exception("Field: $key isn't Allowed");
		}

		if (isset($this->setters[$key]))
			$val = $this->setters[$key]($val);

		$this->bag[$key] = $val;
	}

	public function __unset($key) {
		unset($this->bag[$key]);
	}

	public function __isset($key) {
		return isset($this->allowed[$key]);
	}

	/* iterator interface */
	public function current() {
		return current($this->bag);
	}
	public function key() {
		return key($this->bag);
	}
	public function next() {
		return next($this->bag);
	}
	public function rewind() {
		// remove nulls
		foreach($this->bag as $k => $v) {
			if (is_null($v)) {
				unset($this->bag[$k]);
			}
		}
		reset($this->bag);
	}
	public function valid() {
		return (boolean) current($this->bag);
	}

	public function offsetExists($index) {
		return $this->__isset($index);
	}

	public function offsetGet($index) {
		return $this->__get($index);
	}
	public function offsetSet($index,$val) {
		$this->__set($index,$val);
	}
	public function offsetUnset($index) {
		$this->__unset($index);
	}

	public function count() {
		// ignore nulls
		$i = 0;
		foreach($this->bag as $v) {
			if (!is_null($v)) {
				++$i;
			}
		}
		return $i;
	}
}
