<?php

namespace TinyCms\NodeProvider\Type\Asset;

use TinyCms\NodeProvider\Classes\BaseNodeType;

class AssetType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $typeName string
	 * @param $parentType TinyCms\NodeProvider\Library\EntityTypeInterface
	 * @param $description array
	 */
	public function __construct($parentType=null, $description=array())
	{
		parent::__construct('TinyCmsNodeProvider/Asset', $parentType, $description);
	}
}
