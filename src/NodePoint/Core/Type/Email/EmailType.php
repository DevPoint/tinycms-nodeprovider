<?php

namespace NodePoint\Core\Type\Email;

use NodePoint\Core\Classes\BaseType;

class EmailType extends BaseType {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->typeName = 'Core/Email';
	}

	/*
	 * @param $fieldName string
	 * @return int - None, Int, Text
	 */
	public function getSearchKeyType()
	{
		return self::STORAGE_TEXT;
	}

	/*
	 * @param $value mixed
	 * @return mixed string or int
	 */
	public function searchKeyFromValue($value)
	{
		return mb_strtolower($value, 'UTF-8');
	}
}