<?php
/**
 * Key value coding for PHP.
 *
 * @since 08-03-2013
 * @author Ward van Teijlingen
 */
class KVC {

	const COLLECTION_SELECTOR = "#";
	const COLLECTION_OPERATOR = "@";

	public static function getValue($subject, $keyPath) {
		$kvc = new KVC();
		return $kvc->getValueAtKeyPath($subject, $keyPath);
	}

	public static function getValues($subjects, $keyPath) {
		$kvc = new KVC();
		return $kvc->getValuesAtKeyPath($subjects, $keyPath);
	}

	public static function set($objects, $keyPath, $value) {
		$kvc = new KVC();
		return $kvc->setValueAtKeyPath($objects, $keyPath, $value);
	}


	public function getValuesAtKeyPath($subjects, $keyPath) {
		//Because $objects can be an array of subjects or the subject array itself, we wrap it in an array.
		$returnValues = array();
		foreach ($subjects as $subject) {
			$returnValues[] = $this->getValueAtKeyPath($subject, $keyPath);
		}

		return $returnValues;
	}

	public function getValueAtKeyPath($subject, $keyPath) {
		$keyPathComponents = explode(".", $keyPath);

		//Loop through all path components for the current subject
		foreach ($keyPathComponents as $pathComponent) {

			//Check if this component is an operator or selector
			if(substr($pathComponent, 0, 1) == self::COLLECTION_SELECTOR) {
				$name;
				$argument;
				$this->_parseSelectorComponent($pathComponent, $name, $argument);

				$collectionSelectorFunction = "_collectionSelector_" . $name;
				$subject = $this->$collectionSelectorFunction($subject, $argument);

			} elseif(substr($pathComponent, 0, 1) == self::COLLECTION_OPERATOR) {
				//TODO: Implement
				//
			} else {
				$subject = $this->_getValueForKey($subject, $pathComponent);
			}
		}

		return $subject;
	}

	public function setValueAtKeyPath(&$objects, $keyPath, $value) {

		$subjects = array($objects);
		$keyPathComponents = explode(".", $keyPath);

		foreach ($subjects as $subject) {
			$currentSubject =& $subject;


			foreach ($keyPathComponents as $pathComponent) {
				//Check if this component is an operator or selector
				if(substr($pathComponent, 0, 1) == self::COLLECTION_SELECTOR) {
					$name;
					$argument;
					$this->_parseSelectorComponent($pathComponent, $name, $argument);

					$collectionSelectorFunction = "_collectionSelector_" . $name;
					$currentSubject =& $this->$collectionSelectorFunction($currentSubject, $argument);

				} elseif(substr($pathComponent, 0, 1) == self::COLLECTION_OPERATOR) {
					//TODO: Implement
					//
				} else {
					$currentSubject =& $this->_getValueForKey($currentSubject, $pathComponent);
					var_dump($currentSubject);
				}
			}
			
			$currentSubject = $value;
			var_dump($currentSubject);
		}
	}

	function is_ref_to(&$a, &$b)
	{
	    $t = $a;
	    if($r=($b===($a=1))){ $r = ($b===($a=0)); }
	    $a = $t;
	    return $r;
	}


	/**
	 * Returns the value of key.
	 */
	protected function _getValueForKey($subject, $key) {
		$value = null;

		if(is_array($subject)) {
			$value = $subject[$key];
		} elseif(is_object($subject)) {
			$value = $subject->$key();
		} else {
			die("Can only operate on array or object!");
		}

		return $value;
	}


	protected function _parseSelectorComponent($component, &$name, &$argument) {
		$collectionSelector = substr($component, 1);
		$collectionSelectorParts = explode(":", $collectionSelector);
		$name = $collectionSelectorParts[0];
		$argument = count($collectionSelectorParts > 1) ? $collectionSelectorParts[1] : null;
	}


	/**
	 * #last
	 * Returns last item of array
	 */
	protected function _collectionSelector_last($subjects, $argument = null) {
		return end($subjects);
	}


	/**
	 * #first
	 * Returns first item of array
	 */
	protected function _collectionSelector_first($subjects, $argument = null) {
		return $subjects[0];
	}

	/**
	 * #index:[index]
	 * Returns first item of array
	 */
	protected function _collectionSelector_index($subjects, $argument = null) {
		return $subjects[$argument];
	}


	/**
	 * #from:[index]
	 * Returns subarray starting at argument inclusive
	 */
	protected function _collectionSelector_from($subjects, $argument = null) {
		return array_slice($subjects, $argument);
	}


	/**
	 * #to:[index]
	 * Returns subarray from 0 up to argument inclusive
	 */
	protected function _collectionSelector_to($subjects, $argument = null) {
		return array_slice($subjects, 0, $argument + 1, true);
	}
}
?>