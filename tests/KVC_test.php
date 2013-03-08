<?php
require_once('simpletest/autorun.php');
require_once('../KVC.php');

class TestOfKVC extends UnitTestCase {
	function testKVCGetsFirstLevelValue() {
		$kvc = new KVC();
		$subject = array("first" => "item_1", "second" =>"item_2", "third" => "item_3");

		$result = $kvc->getValueAtKeyPath($subject, "second");
		$this->assertSame($result, "item_2");
	}

	function testKVCGetsFirstLevelValues() {
		$kvc = new KVC();

		$subjects = array();
		for ($i=0; $i < 3; $i++) { 
			$subjects[] = array("first" => "item_1", "second" =>"item_2", "third" => "item_3");
		}

		$result = $kvc->getValuesAtKeyPath($subjects, "second");

		$this->assertTrue(is_array($result));
		$this->assertTrue(count($result) == 3);
		$this->assertSame($result[0], "item_2");
		$this->assertSame($result[1], "item_2");
		$this->assertSame($result[2], "item_2");
	}

	function testKVCFirstSelector() {
		$kvc = new KVC();
		$subject = array("item_1", "item_2", "item_3");

		$result = $kvc->getValueAtKeyPath($subject, "#first");
		$this->assertSame($result, "item_1");
	}

	function testKVCLastSelector() {
		$kvc = new KVC();
		$subject = array("item_1", "item_2", "item_3");

		$result = $kvc->getValueAtKeyPath($subject, "#last");
		$this->assertSame($result, "item_3");
	}

	function testKVCIndexSelector() {
		$kvc = new KVC();
		$subject = array("item_1", "item_2", "item_3");

		$result = $kvc->getValueAtKeyPath($subject, "#index:1");
		$this->assertSame($result, "item_2");
	}

	function testKVCFromSelector() {
		$kvc = new KVC();
		$subject = array("item_1", "item_2", "item_3", "item_4", "item_5", "item_6");

		$result = $kvc->getValueAtKeyPath($subject, "#from:4");
		$this->assertTrue(is_array($result));
		$this->assertTrue(count($result) == 2);
		$this->assertSame($result[0], "item_5");
		$this->assertSame($result[1], "item_6");
	}

	function testKVCToSelector() {
		$kvc = new KVC();
		$subject = array("item_1", "item_2", "item_3", "item_4", "item_5", "item_6");

		$result = $kvc->getValueAtKeyPath($subject, "#to:2");
		$this->assertTrue(is_array($result));
		$this->assertTrue(count($result) == 3);
		$this->assertSame($result[0], "item_1");
		$this->assertSame($result[1], "item_2");
		$this->assertSame($result[2], "item_3");
	}

}
?>