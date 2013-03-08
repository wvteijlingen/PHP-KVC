<?php

class TestClass {
	public $publicVariable = 'public';
	private $_privateVariable = 'private';

	public function getPrivateVariable() {
		return $this->_privateVariable;
	}
}
?>