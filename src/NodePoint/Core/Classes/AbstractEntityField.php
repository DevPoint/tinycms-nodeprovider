<?php

namespace NodePoint\Core\Classes;

use NodePoint\Core\Library\EntityFieldInterface;

abstract class AbstractEntityField implements EntityFieldInterface {

	/*
	 * @var string with fieldName
	 */
	protected $name;

	/*
	 * @var mixed string with language code
	 */
	protected $lang;

	/*
	 * @var int
	 */
	protected $sortIndex;

	/*
	 * @var string
	 */
	protected $id;

	/*
	 * @var boolean
	 */
	protected $lazyLoaded;

	/*
	 * @var string with type name
	 */
	protected $lazyLoadTypeName;

	/*
	 * @param $name string with fieldName
	 * @param mixed string with language code
	 */
	protected function __construct($name, $lang)
	{
		$this->name = $name;
		$this->lang = $lang;
		$this->id = null;
		$this->sortIndex = 0;
		$this->lazyLoaded = false;
		$this->lazyLoadTypeName = null;
	}

	/*
	 * @param string
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/*
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/*
	 * @param string with fieldName
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/*
	 * @return string with fieldName
	 */
	public function getName()
	{
		return $this->name;
	}

	/*
	 * @param mixed string with language code
	 */
	public function setLanguage($lang)
	{
		$this->lang = $lang;
	}

	/*
	 * @return mixed string with language code
	 */
	public function getLanguage()
	{
		return $this->lang;
	}

	/*
	 * @param $index int
	 */
	public function setSortIndex($index)
	{
		$this->sortIndex = $index;
	}

	/*
	 * @return int
	 */
	public function getSortIndex()
	{
		return $this->sortIndex;
	}

	/*
	 * @param $name string
	 */
	public function setLazyLoadTypeName($typeName)
	{
		$this->lazyLoadTypeName = $typeName;
	}
	
	/*
	 * @return string
	 */
	public function getLazyLoadTypeName()
	{
		return $this->lazyLoadTypeName;
	}

	/*
	 * @return boolean
	 */
	public function isLazyLoaded()
	{
		return (null !== $this->lazyLoadTypeName);
	}

	/*
	 * @return boolean
	 */
	public function isArray()
	{
		return false;
	}

	/*
	 * @return int
	 */
	public function getArraySize()
	{
		return 0;
	}

	/*
	 * @param $item NodePoint\Core\Library\EntityFieldInterface
	 */
	public function addArrayItem(EntityFieldInterface $item)
	{
		// TODO: Exception: no array type
	}

	/*
	 * @return boolean true for node types
	 */
	public function getArrayItems()
	{
		return null;
	}

	/*
	 * @return boolean true for node types
	 */
	public function getArrayItem($index)
	{
		return null;
	}
}