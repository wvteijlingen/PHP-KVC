<?php
/**
 * Key value coding for PHP.
 * https://github.com/wvteijlingen/PHP-KVC/
 *
 * @since 08-03-2013
 * @author Ward van Teijlingen
 */
class KVC {

	const COLLECTION_SELECTOR = "#";
	const COLLECTION_OPERATOR = "@";

	/*
	 * --------------------------------------------
	 * Static get and set functions for convenience.
	 * --------------------------------------------
	 */

	public static function getValue($subject, $keyPath) {
		$kvc = new KVC();
		return $kvc->getValueAtKeyPath($subject, $keyPath);
	}

	public static function getValues($subjects, $keyPath) {
		$kvc = new KVC();
		return $kvc->getValuesAtKeyPath($subjects, $keyPath);
	}


	/*
	 * --------------------------------------------
	 * Get
	 * --------------------------------------------
	 */

	public function getValueAtKeyPath($subject, $keyPath) {
		$values = $this->getValuesAtKeyPath(array($subject), $keyPath);
		return $values[0];
	}

	public function getValuesAtKeyPath($subjects, $keyPath) {
		$keyPathComponents = explode(".", $keyPath);

		$returnValues = array();

		foreach ($subjects as $subject) {
			foreach ($keyPathComponents as $pathComponent) {

				//Collection selector
				if(substr($pathComponent, 0, 1) == self::COLLECTION_SELECTOR) {
					$name;
					$argument;
					$this->_parseSelectorComponent($pathComponent, $name, $argument);

					$collectionSelectorFunction = "_collectionSelector_" . $name;
					$subject = $this->$collectionSelectorFunction($subject, $argument);

				//Collection operator
				} elseif(substr($pathComponent, 0, 1) == self::COLLECTION_OPERATOR) {
					//TODO: Implement

				//Regular key
				} else {
					$subject = $this->_getValueForKey($subject, $pathComponent);
				}
			}

			$returnValues[] = $subject;
		}

		return $returnValues;
	}


	/*
	 * --------------------------------------------
	 * Set
	 * --------------------------------------------
	 */
	
	public function setValuesAtKeyPath(&$objects, $keyPath, $value) { 
		foreach ($objects as &$object) {
			$this->setValueAtKeyPath($object, $keyPath, $value);
		}
	}

	public function setValueAtKeyPath(&$object, $keyPath, $value) {
		$keyPathComponents = explode(".", $keyPath);

		$currentSubject = &$object;

		foreach ($keyPathComponents as $pathComponent) {
			$new = &$this->_getValueForKey($currentSubject, $pathComponent, true);
			unset($currentSubject);
			$currentSubject = &$new;
		}

		$currentSubject = $value;

		unset($currentSubject);
	}


	/*
	 * --------------------------------------------
	 * Get helper
	 * --------------------------------------------
	 */
	
	protected function &_getValueForKey(&$subject, $key, $byRef = false) {;
		$value = null;

		//Array type
		if(is_array($subject)) {
			if(array_key_exists($key, $subject) ) {
				if($byRef) {
					$value =& $subject[$key];
				} else {
					$value = $subject[$key];
				}
			} else {
				throw new Exception('Key ' . $key . ' not found on array ' . $subject . '.');
			}

		//Object type
		} elseif(is_object($subject)) {

			//Variable
			if(property_exists($subject, $key)) {
				if($byRef) {
					$value =& $subject->$key;
				} else {
					$value = $subject->$key;
				}

			//Getter
			} elseif(method_exists($subject, 'get' . ucfirst($key) ) ) {
				$getter = 'get' . ucfirst($key);

				if($byRef) {
					$value =& $subject->$getter();
				} else {
					$value = $subject->$getter;
				}

			} else {
				throw new Exception('Key ' . $key . ' not found on object ' . get_class($subject) . '.');
			}

		} else {
			 throw new Exception('KVC only works with objects or arrays, ' . gettype($subject) . ' given.');
		}

		return $value;
	}


	/*
	 * --------------------------------------------
	 * Collection selectors
	 * --------------------------------------------
	 */

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
