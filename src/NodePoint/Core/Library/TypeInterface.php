<?php

namespace NodePoint\Core\Library;

interface TypeInterface {

	/*
	 * Constants for storage types
	 */
	const STORAGE_NONE = 	0;
	const STORAGE_TEXT = 	1;
	const STORAGE_INT = 	2;
	const STORAGE_FLOAT = 	3;
	const STORAGE_ENTITY = 	4;

	/*
	 * @param $type string
	 */
	public function setTypeName($typeName);

	/*
	 * @return string
	 */
	public function getTypeName();

	/*
	 * @param $type string
	 * @return boolean true if type or any of the parent types matches
	 */
	public function isTypeName($typeName);

	/*
	 * @param $type string
	 * @return boolean true if type matches
	 */
	public function isTypeNameExact($typeName);
	
	/*
	 * @param $className string
	 */
	public function setClassName($className);

	/*
	 * @return string
	 */
	public function getClassName();

	/*
	 * @return boolean true for object types
	 */
	public function isObject();

	/*
	 * @return boolean true for entity types
	 */
	public function isEntity();

	/*
	 * @return boolean true for node types
	 */
	public function isNode();

	/*
	 * @return boolean true if type is refering to an entity
	 */
	public function isReference();

	/*
	 * @return string
	 */
	public function getReferenceTypeName();

	/*
	 * @param $fieldName string
	 * @return int - Int, Float, Text, Entity
	 */
	public function getStorageType();

	/*
	 * @param $value mixed
	 * @return mixed string or int
	 */
	public function searchKeyFromValue($value);

	/*
	 * @param $object mixed
	 * @return mixed - array or input parameter type
	 */
	public function objectToArray($object);

	/*
	 * @param $value mixed
	 * @return mixed - object or input parameter type
	 */
	public function objectFromArray(&$value);

	/*
	 * @param $object object
	 * @return string
	 */
	public function objectToSerialized($object);

	/*
	 * @param $value string
	 * @return object
	 */
	public function objectFromSerialized(&$serializedValue);
	
	/*
	 * Calculate further values from the given properties
	 */
	public function finalize();

}