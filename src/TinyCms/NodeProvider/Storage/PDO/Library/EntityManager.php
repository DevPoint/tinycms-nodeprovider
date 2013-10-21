<?php

namespace TinyCms\NodeProvider\Storage\PDO\Library;

use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Storage\Library\EntityManagerInterface;
use TinyCms\NodeProvider\Storage\PDO\Library\EntityStorageProxy;

class EntityManager implements EntityManagerInterface {

	/*
	 * @var PDO
	 */
	protected $conn;

	/*
	 * @var array of TinyCms\NodeProvider\Storage\Library\EntityRepositoryInterface
	 */
	protected $repositories;

	/*
	 * @var array of TinyCms\NodeProvider\Library\EntityInterface
	 */
	protected $entitiesToUpdate;

	/*
	 * @param $conn \PDO
	 */
	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->repositories = array();
		$this->entitiesToUpdate = array();
	}

	/*
	 * @return \PDO
	 */
	public function getConnection()
	{
		return $this->conn;
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function onUpdate(EntityInterface $entity)
	{
		$this->entitiesToUpdate[] = $entity;
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function persist(EntityInterface $entity)
	{
		$storageProxy = $entity->_getStorageProxy();
		if (!$storageProxy)
		{
			$type = $entity->_type();
			$typeName = $type->getTypeName();
			if (!isset($this->repositories[$typeName]))
			{
				$repositoryClass = $type->getStorageRepositoryClass();
				if (!$repositoryClass)
				{
					$repositoryClass = "\\TinyCms\\NodeProvider\\Storage\\PDO\\Library\\EntityRepository";
				}
				$repository = new $repositoryClass($this->conn, $this, $type);
				$this->repositories[$typeName] = $repository;
			}
			$storageProxy = new EntityStorageProxy($this, $entity);
			$entity->_setStorageProxy($storageProxy);
			$storageProxy->updateAllFields();
		}
	}


	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 * @param $storageProxy TinyCms\NodeProvider\Storage\PDO\Library\EntityStorageProxy
	 */
	protected function saveEntity(EntityInterface $entity, EntityStorageProxy $storageProxy)
	{
		// find entities which are necessary to
		// save before current entity
		$callTypeGet = 'get';
		$type = $entity->_type();
		$fieldNames = $type->getFieldNames();
		$relatedEntitiesCanditates = array();
		foreach ($fieldNames as $fieldName)
		{
			if ($entity->hasFieldStorageColumn($fieldName))
			{
				$fieldType = $entity->_fieldType($fieldName);
				if ($fieldType->isEntity())
				{
					$magicCallGetField = $type->getFieldMagicCallName($fieldName, $callTypeGet);
					if ($type->isFieldArray($fieldName))
					{
						$relatedEntities = $entity->{$magicCallGetField}();
						if (is_array($relatedEntities))
						{
							foreach ($relatedEntities as $relatedEntity)
							{
								$relatedEntitiesCanditates[] = $relatedEntity;
							}
						}
					}
					else
					{
						$relatedEntity = $entity->{$magicCallGetField}();
						if (null !== $relatedEntity)
						{
							$relatedEntitiesCanditates[] = $relatedEntity;
						}
					}
				}
			}
		}

		// walk through canditates to check
		// if the should have been saved before
		foreach ($relatedEntitiesCanditates as $relatedEntity)
		{
			$relatedType = $relatedEntity->_type();
			$magicCallGetId = $relatedType->getFieldMagicCallName($relatedType->getIdFieldName(), $callTypeGet);
			$relatedEntityId = $relatedEntity->{$magicCallGetId}();
			if (null === $relatedEntityId)
			{
				$relatedStorageProxy = $relatedEntity->_getStorageProxy();
				$relatedEntityManager = $relatedStorageProxy->getEntityManager();
				$relatedEntityManager->save($relatedEntity);
			}
		}

		// save current entity
		$typeName = $type->getTypeName();
		$repository = $this->repositories[$typeName];
		$repository->save($entity);
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function save(EntityInterface $entity)
	{
		$storageProxy = $entity->_getStorageProxy();
		if ($storageProxy && $storageProxy->hasUpdate())
		{			
			$this->saveEntity($entity, $storageProxy);
			$storageProxy->resetUpdate();
		}
	}

	/*
	 * Writes all changes back to storage
	 */
	public function flush()
	{
		if (!empty($this->entitiesToUpdate))
		{
			foreach ($this->entitiesToUpdate as $entity)
			{
				$storageProxy = $entity->_getStorageProxy();
				if ($storageProxy->hasUpdate())
				{			
					$this->saveEntity($entity, $storageProxy);
					$storageProxy->resetUpdate();
				}
			}
			$this->entitiesToUpdate = array();
		}
	}
}