<?php

header("Content-Type:text/plain; charset=utf-8");

use TinyCms\NodeProvider\Library\MagicFieldCallInfo;
use TinyCms\NodeProvider\Type\Entity\Entity;
use TinyCms\NodeProvider\Type\Node\Node;
use TinyCms\NodeProvider\Type\Position2d\Position2d;

// create types
$parentType = new TinyCms\NodeProvider\Type\Entity\EntityType();
$stringType = new TinyCms\NodeProvider\Type\String\StringType();
$position2dType = new TinyCms\NodeProvider\Type\Position2d\Position2dType();
$entityType = new TinyCms\NodeProvider\Type\Node\NodeType($parentType);
$entityType->setFieldType('alias', $stringType);
$entityType->setFieldType('parent', $entityType);
$entityType->setFieldType('name', $stringType);
$entityType->setFieldDescription('name', array('hasOptions'=>true,'staticOptions'=>array('wilfried','carmen','david','julian','milena')));
$entityType->setFieldType('body', $stringType);
$entityType->setFieldDescription('body', array('i18n'=>true));
$entityType->setFieldType('geoLocation', $position2dType);
$entityType->setFieldType('info', $stringType);
$entityType->setFieldDescription('info', array('isStatic'=>true, 'i18n'=>true));
$entityType->setMagicFieldCallInfo('setParent', new MagicFieldCallInfo('parent', '_setMagicFieldCall'));
$entityType->setMagicFieldCallInfo('getParent', new MagicFieldCallInfo('parent', '_getMagicFieldCall'));
$entityType->setMagicFieldCallInfo('setAlias', new MagicFieldCallInfo('alias', '_setMagicFieldCall'));
$entityType->setMagicFieldCallInfo('getAlias', new MagicFieldCallInfo('alias', '_getMagicFieldCall'));
$entityType->setMagicFieldCallInfo('setName', new MagicFieldCallInfo('name', '_setMagicFieldCall'));
$entityType->setMagicFieldCallInfo('getName', new MagicFieldCallInfo('name', '_getMagicFieldCall'));
$entityType->setMagicFieldCallInfo('validateName', new MagicFieldCallInfo('name', '_validateMagicFieldCall'));
$entityType->setMagicFieldCallInfo('setBody', new MagicFieldCallInfo('body', '_setMagicFieldCallI18n'));
$entityType->setMagicFieldCallInfo('getBody', new MagicFieldCallInfo('body', '_getMagicFieldCallI18n'));
$entityType->setMagicFieldCallInfo('validateBody', new MagicFieldCallInfo('body', '_validateMagicFieldCall'));
$entityType->setMagicFieldCallInfo('setGeoLocation', new MagicFieldCallInfo('geoLocation', '_setMagicFieldCall'));
$entityType->setMagicFieldCallInfo('getGeoLocation', new MagicFieldCallInfo('geoLocation', '_getMagicFieldCall'));
$entityType->setMagicFieldCallInfo('getInfo', new MagicFieldCallInfo('info', '_getMagicFieldStaticCallI18n'));

// language codes
$langA = "de";
$langB = "en";

// set static values
$entityType->setFieldStaticValueI18n('info', $langA, "Informationsunterlagen");
$entityType->setFieldStaticValueI18n('info', $langB, "Information material");
$entityType->setFieldOptionReferences('name', $langA, array(
				'wilfried' => 'Wilfried',
				'carmen' => 'Carmen',
				'david' => 'David',
				'julian' => 'Julian',
				'milena' => 'Milena'));
$entityType->setFieldOptionReferences('name', $langB, array(
				'wilfried' => 'Wilfred',
				'carmen' => 'Carmen',
				'david' => 'David',
				'julian' => 'Julian',
				'milena' => 'Milena'));

// create object instance
$parent = new Node($entityType);
$parent->setAlias("carmen-und-wilfried");
$parent->setName("Carmen und Wilfried");
$geoLocation = new Position2d();
$geoLocation->set(41.501, 14.502);
$parent->setGeoLocation($geoLocation);

$arrObjects = array();
$object = new Node($entityType);
$object->setParent($parent);
$object->setAlias("julian-brabsche");
$object->setName("Julian Brabsche");
$object->setBody($langA, "Hier kommt Julian, unser Mathe-Genie!");
$object->setBody($langB, "Here comes Julian, our mathe genious!");
$geoLocation = new Position2d();
$geoLocation->set(43.001, 15.002);
$object->setGeoLocation($geoLocation);
$arrObjects[] = $object;

$object = new Node($entityType);
$object->setParent($parent);
$object->setAlias("david-brabsche");
$object->setName("David Brabsche");
$object->setBody($langA, "Hier kommt unser lieber David!");
$object->setBody($langB, "Here comes our cute David!");
$geoLocation = new Position2d();
$geoLocation->set(43.001, 15.002);
$object->setGeoLocation($geoLocation);
$arrObjects[] = $object;

$arrGeoLocation = $object->_fieldType('geoLocation')->objectToValue($object->getGeoLocation());


// output test result
echo "Test succeeded\n";
echo "----------------\n";
foreach ($arrObjects as $object)
{
	$langOut = $langB;
	echo $object->getName() . "\n";
	echo $object->getBody($langOut) . "\n";
	echo "Meine Eltern heißen " . $object->getParent()->getName() . "\n";
	echo "Validate Field 'Name': " . $object->validateName("Carmen") . "\n";
	echo "Validate Field 'Body': " . $object->validateBody("Carmen") . "\n";
	echo "Static Value: " . $object->getParent()->getInfo($langOut) . "\n";
	echo "Du findest mich an folgenden Geokoordination: " . $arrGeoLocation['x'] . ', ' . $arrGeoLocation['y'] . "\n";
	echo "Option references: " . implode(', ', $entityType->getFieldOptionReferences('name', $langOut)) . "\n";
	echo "\n";
}
