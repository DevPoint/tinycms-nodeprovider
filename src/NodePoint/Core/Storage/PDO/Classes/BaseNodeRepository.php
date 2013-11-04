<?php

namespace NodePoint\Core\Storage\PDO\Classes;

use NodePoint\Core\Library\TypeInterface;
use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Library\EntityFieldInterface;
use NodePoint\Core\Library\EntityTypeInterface;
use NodePoint\Core\Storage\Library\EntityManagerInterface;

class BaseNodeRepository extends AbstractEntityTableRepository {

	/*
	 * @param $conn \PDO
	 * @param $em NodePoint\Core\Storage\Library\EntityManagerInterface
	 */
	public function __construct(\PDO $conn, EntityManagerInterface $em)
	{
		parent::__construct($conn, $em);
	}

	/*
	 * @param $entity NodePoint\Core\Library\EntityTypeInterface
	 * @return array of fieldNames
	 */
	protected function _getStorageFieldNames(EntityTypeInterface $type)
	{
		$result = array();
		$fieldNames = $type->getFieldNames();
		foreach ($fieldNames as $fieldName)
		{
			$fieldInfo = $type->getFieldInfo($fieldName);
			if (!$fieldInfo->isReadOnly() && 0 != $fieldInfo->getStorageType())
			{
				$result[] = $fieldName;
			}
		}
		return $result;
	}

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	protected function _update(EntityInterface $entity)
	{
		// prepare serialization of the entity
		$type = $entity->_type();
		$fields = $entity->_fields();
		$storageProxy = $entity->_getStorageProxy();
		$fieldNames = $storageProxy->getUpdateFieldNames();

		// filter fields and update them in the entity table
		$mapFieldNames = array_fill_keys($fieldNames, true);
		$entityId = $this->_getEntityId($entity);
		$entityRow = $this->_serializeFieldsToRow($type, $fields, $mapFieldNames, $entityId);
		if (!empty($entityRow))
		{
			$this->_updateRow($entityRow);
		}
			
		// filter fields and update them in the entity fields table
		$entityFieldRows = $this->_serializeFieldsToFieldRows($type, $fields, $mapFieldNames, $entityId);
		$this->_saveFieldRows($entityFieldRows);
	}

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	protected function _insert(EntityInterface $entity)
	{
		// prepare serialization of the entity
		$type = $entity->_type();
		$fields = $entity->_fields();
		$fieldNames = $this->_getStorageFieldNames($type);
		$mapFieldNames = array_fill_keys($fieldNames, true);

		// filter fields and insert them into the entity table
		$entityRow = $this->_serializeFieldsToRow($type, $fields, $mapFieldNames, null);
		$entityId = $this->_insertRow($entityRow);
		$this->_setEntityId($entity, $entityId);

		// filter fields and insert them into the entity fields table
		$entityFieldRows = $this->_serializeFieldsToFieldRows($type, $fields, $mapFieldNames, $entityId);
		$this->_insertFieldRows($entityFieldRows);
	}

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	public function save(EntityInterface $entity)
	{
		$type = $entity->_type();
		$entityId = $this->_getEntityId($entity);
		if (null !== $entityId)
		{
			$this->_update($entity);
		}
		else
		{
			$this->_insert($entity);
		}
	}

	/*
	 * @param $typeName string
	 * @param $row entity table row
	 * @param $mapFieldNames array indexed by fieldName
	 * @param $lang mixed string or array of string
	 * @return NodePoint\Core\Library\EntityInterface
	 */
	public function read($typeName, $row, $lang=null, $mapFieldNames=null)
	{
		$entityId = $row['id'];
		$type = $this->em->getTypeFactory()->getType($typeName);
		$fields = $this->_unserializeFieldsFromRow($type, $row);

		$fieldRows = $this->_selectFieldRows($entityId, $lang);
		$fields = array_merge($fields, $this->_unserializeFieldsFromFieldRows($type, $fieldRows));

		$entityClass = $type->getClassName();
		$entity = new $entityClass($type, $fields);
		$this->em->persist($entity);
		return $entity;
	}

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 * @param $field NodePoint\Core\Library\EntityFieldInterface
	 * @return boolean
	 */
	public function loadField(EntityInterface $entity, EntityFieldInterface $field)
	{
		$type = $entity->_type();
		$fieldName = $field->getName();
		//if ($fieldName == $this->invTableFields['entities']['parent_id'])
		//{
		//}


		return false;
	}

	/*
	 * @param $entityId string 
	 * @return NodePoint\Core\Library\EntityInterface
	 */
	public function find($entityId)
	{
		// read entity table row
		$row = $this->_selectRow($entityId);
		if (null === $row)
		{
			return null;
		}
		// select repository assigned to that type
		$typeName = $row['type'];
		$repository = $this->em->getRepository($typeName);
		if (null === $repository)
		{
			// TODO: Exception: no repository for this type available
			return null;
		}
		// create entity by reading this repository
		return $repository->read($typeName, $row, null, null);
	}
}
