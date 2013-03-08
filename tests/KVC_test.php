<?php
require_once('simpletest/autorun.php');
require_once('../KVC.php');
require_once('TestClass.php');

class TestOfKVC extends UnitTestCase {
	/** Tests getting single value on first level */
	function testKVCGetsSingleFirstLevelValueWithArray() {
		$kvc = new KVC();
		$subject = array("first" => "item_1", "second" =>"item_2", "third" => "item_3");

		$result = $kvc->getValueAtKeyPath($subject, "second");
		$this->assertSame($result, "item_2");
	}

	/** Tests getting multiple values on first level */
	function testKVCGetsMultipleFirstLevelValuesWithArray() {
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

	/** Tests getting single value on second level */
	function testKVCGetsSingleDeepValueWithArray() {
		$kvc = new KVC();
		$subject = array("first" => "item_1",
						 "deep" => array("deepFirst" => "deep_item_1", 
						 				 "deepSecond" => "deep_item_2",
						 				 "deepThird" => "deep_item_3")
						 );

		$result = $kvc->getValueAtKeyPath($subject, "deep.deepSecond");
		$this->assertSame($result, "deep_item_2");
	}

	/** Tests getting multiple values on second level */
	function testKVCGetsMultipleDeepValuesWithArray() {
		$kvc = new KVC();

		$subjects = array();
		for ($i=0; $i < 3; $i++) { 
			$subjects[] = array("first" => "item_1",
								"deep" => array("deepFirst" => "deep_item_1", 
												"deepSecond" => "deep_item_2",
												"deepThird" => "deep_item_3")
								);
		}

		$result = $kvc->getValuesAtKeyPath($subjects, "deep.deepSecond");

		$this->assertTrue(is_array($result));
		$this->assertTrue(count($result) == 3);
		
		foreach ($result as $resultItem) {
			$this->assertSame($resultItem, "deep_item_2");
		}
	}

	/** Tests first selector */
	function testKVCFirstSelectorWithArray() {
		$kvc = new KVC();
		$subject = array("item_1", "item_2", "item_3");

		$result = $kvc->getValueAtKeyPath($subject, "#first");
		$this->assertSame($result, "item_1");
	}

	/** Tests lasts selector */
	function testKVCLastSelectorWithArray() {
		$kvc = new KVC();
		$subject = array("item_1", "item_2", "item_3");

		$result = $kvc->getValueAtKeyPath($subject, "#last");
		$this->assertSame($result, "item_3");
	}

	/** Tests index selector */
	function testKVCIndexSelectorWithArray() {
		$kvc = new KVC();
		$subject = array("item_1", "item_2", "item_3");

		$result = $kvc->getValueAtKeyPath($subject, "#index:1");
		$this->assertSame($result, "item_2");
	}

	/** Tests from selector */
	function testKVCFromSelectorWithArray() {
		$kvc = new KVC();
		$subject = array("item_1", "item_2", "item_3", "item_4", "item_5", "item_6");

		$result = $kvc->getValueAtKeyPath($subject, "#from:4");
		$this->assertTrue(is_array($result));
		$this->assertTrue(count($result) == 2);
		$this->assertSame($result[0], "item_5");
		$this->assertSame($result[1], "item_6");
	}

	/** Tests to selector */
	function testKVCToSelectorWithArray() {
		$kvc = new KVC();
		$subject = array("item_1", "item_2", "item_3", "item_4", "item_5", "item_6");

		$result = $kvc->getValueAtKeyPath($subject, "#to:2");
		$this->assertTrue(is_array($result));
		$this->assertTrue(count($result) == 3);
		$this->assertSame($result[0], "item_1");
		$this->assertSame($result[1], "item_2");
		$this->assertSame($result[2], "item_3");
	}

	/** Tests getting single value of first level object */
	function testKVCGetsSingleFirstLevelValueWithObject() {
		$subject = array(new TestClass(), new TestClass(), new TestClass());

		$kvc = new KVC();
		$result = $kvc->getValueAtKeyPath($subject, "#index:0");
		$this->assertSame($result, $subject[0]);
	}

	/** Tests getting public variables of objects */
	function testKVCGetsPublicVariableOfObject() {
		$subject = array(new TestClass(), new TestClass(), new TestClass());

		$kvc = new KVC();
		$result = $kvc->getValuesAtKeyPath($subject, "publicVariable");

		$this->assertTrue(is_array($result));
		$this->assertTrue(count($result) == 3);

		foreach ($result as $resultItem) {
			$this->assertSame($resultItem, "public");
		}
	}

	/** Tests calling getter of objects */
	function testKVCGetsGetterOfObject() {
		$subject = array(new TestClass(), new TestClass(), new TestClass());

		$kvc = new KVC();
		$result = $kvc->getValuesAtKeyPath($subject, "privateVariable");

		$this->assertTrue(is_array($result));
		$this->assertTrue(count($result) == 3);

		foreach ($result as $resultItem) {
			$this->assertSame($resultItem, "private");
		}
	}
}
?>