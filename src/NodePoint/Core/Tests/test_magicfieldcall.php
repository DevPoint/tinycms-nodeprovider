<?php

header("Content-Type:text/plain; charset=utf-8");

use NodePoint\Core\Type\Entity\Entity;
use NodePoint\Core\Type\Node\Node;
use NodePoint\Core\Type\Position2d\Position2d;

// language codes
$langA = "de";
$langB = "en";

// create types
$parentType = new \NodePoint\Core\Type\Entity\EntityType();
$stringType = new \NodePoint\Core\Type\String\StringType();
$position2dType = new \NodePoint\Core\Type\Position2d\Position2dType();
$entityType = new \NodePoint\Core\Type\Node\NodeType($parentType);
$entityType->setFieldType('alias', $stringType);
$entityType->setFieldType('parent', $entityType);
$entityType->setFieldType('name', $stringType);
$entityType->setFieldDescription('name', array('hasOptions'=>true,'options'=>array('wilfried','carmen','david','julian','milena')));
$entityType->setFieldType('body', $stringType);
$entityType->setFieldDescription('body', array('i18n'=>true));
$entityType->setFieldType('geolocation', $position2dType);
$entityType->setFieldType('info', $stringType);
$entityType->setFieldDescription('info', array('static'=>true, 'i18n'=>true));
$entityType->finalize();

// set static values
$entityStatic = $entityType->getStaticEntity();
$entityStatic->setInfo($langA, "Informationsunterlagen");
$entityStatic->setInfo($langB, "Information material");

// create object instance
$parent = new Node($entityType);
$parent->setAlias("carmen-und-wilfried");
$parent->setName("Carmen und Wilfried");
$geolocation = new Position2d();
$geolocation->set(41.501, 14.502);
$parent->setGeolocation($geolocation);

$arrObjects = array();
$object = new Node($entityType);
$object->setParent($parent);
$object->setAlias("julian-brabsche");
$object->setName("Julian Brabsche");
$object->setBody($langA, "Hier kommt Julian, unser Mathe-Genie!");
$object->setBody($langB, "Here comes Julian, our mathematics genious!");
$geolocation = new Position2d();
$geolocation->set(43.001, 15.002);
$object->setGeolocation($geolocation);
$arrObjects[] = $object;

$object = new Node($entityType);
$object->setParent($parent);
$object->setAlias("david-brabsche");
$object->setName("David Brabsche");
$object->setBody($langA, "Hier kommt unser lieber David!");
$object->setBody($langB, "Here comes our cute David!");
$geolocation = new Position2d();
$geolocation->set(43.001, 15.002);
$object->setGeolocation($geolocation);
$arrObjects[] = $object;

$arrGeolocation = $object->_fieldType('geolocation')->objectToArray($object->getGeolocation());


// output test result
echo "Test succeeded\n";
echo "----------------\n";
foreach ($arrObjects as $object)
{
	$langOut = $langA;
	echo $object->getName() . "\n";
	echo $object->getBody($langOut) . "\n";
	echo "Meine Eltern heißen " . $object->getParent()->getName() . "\n";
	echo "Validate Field 'Name': " . $object->validateName("Carmen") . "\n";
	echo "Validate Field 'Body': " . $object->validateBody("Carmen") . "\n";
	echo "Static Value: " . $object->getInfo($langOut) . "\n";
	echo "Du findest mich an folgenden Geokoordination: " . $arrGeolocation['x'] . ', ' . $arrGeolocation['y'] . "\n";
	echo "Name Options: " . implode(', ', $object->_type()->getFieldOptions('name')) . "\n";
	echo "\n";
}