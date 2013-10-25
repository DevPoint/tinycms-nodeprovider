<?php

namespace TinyCms\NodeProvider\Classes;

use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Storage\Library\EntityStorageProxyInterface;

class BaseEntity extends AbstractEntity {

	/*
	 * @var array of TinyCms\NodeProvider\Library\EntityFieldInterface indexed by fieldName
	 */
	protected $fields;

	/*
	 * @var TinyCms\NodeProvider\Storage\Library\EntityStorageProxyInterface
	 */
	protected $storageProxy;

	/*
	 * Constructor
	 *
	 * @param $type TinyCms\NodeProvider\Library\EntityTypeInterface
	 * @param $fields array pf TinyCms\NodeProvider\Library\EntityFieldInterface
	 */
	public function __construct($type, $fields=array())
	{
		// basic construction
		parent::__construct($type);
		$this->storageProxy = null;

		// add static entity fields
		$staticEntity = $type->getStaticEntity();
		if (null !== $staticEntity)
		{
			$this->_addFieldsToCache($staticEntity->_fields());
		}

		// add entity fields
		$this->fields = $fields;
		$this->_addFieldsToCache($fields);
	}

	/*
	 * @return array of TinyCms\NodeProvider\Library\EntityFieldInterface
	 */
	final public function _fields()
	{
		return $this->fields;
	}

	/*
	 * @param $repository TinyCms\NodeProvider\Storage\Library\EntityStorageProxyInterface
	 */
	public function _setStorageProxy(EntityStorageProxyInterface $storageProxy)
	{
		$this->storageProxy = $storageProxy;
	}

	/*
	 * @return TinyCms\NodeProvider\Storage\Library\EntityRepositoryInterface
	 */
	final public function _getStorageProxy()
	{
		return $this->storageProxy;
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
		if (null !== $magicFieldCallInfo)
		{
			return $this->{$magicFieldCallInfo->functionCall}($magicFieldCallInfo->field, $args);
		}
		
		// TODO: Exception: unknown call
		// .
		// .

		return null;
	}
}