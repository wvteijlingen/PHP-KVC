PHP-KVC
=======

PHP-KVC offers a simple Key Value coding class that facilitates easy traversalof nested arrays and objects.

Basic usage
-----------
###Keypaths###
Keypaths are strings in which each path component is separated by a dot.
The component can represent an array key, an instance variable or a getter.
For example, if an object contains a getter `getFoo()`, the path component will be `foo`.


###Getting a single value from a tree###
````php
$tree = array(
  "foo" => array(
    "bar" => array("bar1", "bar2"),
    "baz" => array("baz1", "baz2")
  )
); 

$value = KVC::getValue($tree, "foo.bar");
//$value will be array("bar1", "bar2")
````

###Getting multiple values from a tree###
````php
$tree = array(
  array(
    "foo" => "Bar"
  ),
  array(
    "foo" => "Baz"
  )
); 

$value = KVC::getValues($tree, "foo");
//$value will be array("bar", "baz")
````

###Using collection selectors###
With collection selectors you can select segments from an array.
````php
$tree = array(
  "array1" => array("array1Bar", "array1Baz"),
  "array2" => array("array2Bar", "array2Baz")
); 

$value = KVC::getValue($tree, "array2.@last");
//$value will be "array2Baz"
````

####Supported collection selectors####
- `@first`, gets the first element of an array
- `@last`, gets the last element of an array
- `@index`, gets the element at a specific index
- `@from:n`, gets all elements starting with the nth element inclusive
- `@to:n`, gets all elements up to the the nth element inclusive

Selectors can be combined. For example: `foo.bar.@from:2.@to:5` will select the 2nd, 3rd, 4th and 5th element of an array `bar`, nested within `foo`.
