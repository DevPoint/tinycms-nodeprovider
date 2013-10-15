<?php

namespace TinyCms\NodeProvider\Classes;

use TinyCms\NodeProvider\Library\EntityInterface;

class BaseEntity implements EntityInterface {

	/*
	 * @var TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	protected $type;

	/*
	 * @var array of values indexed by fieldName
	 */
	protected $fields;

	/*
	 * Constructor
	 */
	public function __construct($type, $fields=array())
	{
		$this->type = $type;
		$this->fields = $fields;
	}

	/*
	 * @return TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	final public function _type()
	{
		return $this->type;
	}

	/*
	 * @param $fieldName string
	 * @return TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	final public function _fieldType($fieldName)
	{
		return $this->type->getFieldType($fieldName);
	}

	/*
	 * @param $fieldName string 
	 * @param $args array(0=>language)
	 * @return array
	 */
	protected function _getMagicFieldStaticOptionsCall($fieldName, &$args)
	{
		return $this->type->getFieldStaticOptions($fieldName, $args[0]);
	}

	/*
	 * @param $fieldName string 
	 * @param $args array(0=>value)
	 * @return boolean
	 */
	protected function _validateMagicFieldCall($fieldName, &$args)
	{
		return true;
	}

	/*
	 * @param $fieldName string 
	 * @param $args array(0=>value)
	 * @return TinyCms\NodeProvider\Library\EntityInstance
	 */
	protected function _setMagicFieldCall($fieldName, &$args)
	{
		$this->fields[$fieldName] = $args[0];
		return $this;
	}

	/*
	 * @param $fieldName string 
	 * @return mixed field value
	 */
	protected function _getMagicFieldCall($fieldName)
	{
		if (!isset($this->fields[$fieldName]))
		{
			return null;
		}
		return $this->fields[$fieldName];
	}

	/*
	 * @param $fieldName string 
	 * @return mixed field value
	 */
	protected function _getMagicFieldStaticCall($fieldName)
	{
		return $this->type->getFieldStaticValue($fieldName);
	}

	/*
	 * @param $fieldName string 
	 * @param $args array(0=>language, 1=>value)
	 * @return TinyCms\NodeProvider\Library\EntityInstance
	 */
	protected function _setMagicFieldCallI18n($fieldName, &$args)
	{
		if (!isset($this->fields[$fieldName]))
		{
			$this->fields[$fieldName] = array();	
		}
		$this->fields[$fieldName][$args[0]] = $args[1];
		return $this;
	}

	/*
	 * @param $fieldName string 
	 * @param $args array(0=>language)
	 * @return mixed field value
	 */
	protected function _getMagicFieldCallI18n($fieldName, &$args)
	{
		$lang = $args[0];
		if (!isset($this->fields[$fieldName][$lang]))
		{
			return null;
		}
		return $this->fields[$fieldName][$lang];
	}

	/*
	 * @param $fieldName string 
	 * @param $args array(0=>language)
	 * @return mixed field value
	 */
	protected function _getMagicFieldStaticCallI18n($fieldName, &$args)
	{
		return $this->type->getFieldStaticValueI18n($fieldName, $args[0]);
	}

	/*
	 * @param $name string callName
	 * @param $args array
	 * @return mixed field value or this
	 */
	public function __call($name, $args)
	{
		// get magic field call info
		$magicFieldCallInfo = $this->type->getMagicFieldCallInfo($name);
		if (null === $magicFieldCallInfo)
		{
			// TODO: Exception: unknown call
			return $this;
		}

		// executing function
		return $this->{$magicFieldCallInfo->functionCall}($magicFieldCallInfo->field, $args);
	}
}