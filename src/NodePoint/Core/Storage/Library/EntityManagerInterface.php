<?php

namespace NodePoint\Core\Storage\Library;

use NodePoint\Core\Library\EntityInterface;

interface EntityManagerInterface {

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	public function persist(EntityInterface $entity);

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	public function update(EntityInterface $entity);
	
	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	public function save(EntityInterface $entity);

	/*
	 * Writes all changes back to storage
	 */
	public function flush();
}