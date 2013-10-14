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
$entityType->setFieldDescription('name', array('hasOptions'=>true,'staticOptions'=>array('wilfried','carmen','david','julian','milena')));
$entityType->setFieldType('name', $stringType);
$entityType->setFieldType('body', $stringType);
$entityType->setFieldType('geoLocation', $position2dType);
$entityType->setFieldDescription('body', array('i18n'=>true));
$entityType->setFieldType('parent', $entityType);
$entityType->setFieldType('info', $stringType);
$entityType->setFieldDescription('info', array('isStatic'=>true, 'i18n'=>true));
$entityType->setMagicFieldCallInfo('setParent', new MagicFieldCallInfo('parent', '_setMagicFieldCall'));
$entityType->setMagicFieldCallInfo('getParent', new MagicFieldCallInfo('parent', '_getMagicFieldCall'));
$entityType->setMagicFieldCallInfo('setAlias', new MagicFieldCallInfo('alias', '_setMagicFieldCall'));
$entityType->setMagicFieldCallInfo('getAlias', new MagicFieldCallInfo('alias', '_getMagicFieldCall'));
$entityType->setMagicFieldCallInfo('setName', new MagicFieldCallInfo('name', '_setMagicFieldCall'));
$entityType->setMagicFieldCallInfo('getName', new MagicFieldCallInfo('name', '_getMagicFieldCall'));
$entityType->setMagicFieldCallInfo('setGeoLocation', new MagicFieldCallInfo('geoLocation', '_setMagicFieldCall'));
$entityType->setMagicFieldCallInfo('getGeoLocation', new MagicFieldCallInfo('geoLocation', '_getMagicFieldCall'));
$entityType->setMagicFieldCallInfo('validateName', new MagicFieldCallInfo('name', '_validateMagicFieldCall'));
$entityType->setMagicFieldCallInfo('setBody', new MagicFieldCallInfo('body', '_setMagicFieldCallI18n'));
$entityType->setMagicFieldCallInfo('getBody', new MagicFieldCallInfo('body', '_getMagicFieldCallI18n'));
$entityType->setMagicFieldCallInfo('validateBody', new MagicFieldCallInfo('body', '_validateMagicFieldCall'));
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

$arrObjects = array();

$object = new Node($entityType);
$object->setParent($parent);
$object->setAlias("julian-brabsche");
$object->setName("Julian Brabsche");
$object->setBody($langA, "Here comes Julian, our mathe genious!");
$geoLocation = new Position2d();
$geoLocation->set(43.0, 15.0);
$object->setGeoLocation($geoLocation);
$arrObjects[] = $object;

$arrGeoLocation = $object->_fieldType('geoLocation')->objectToValue($object->getGeoLocation());


// output test result
echo "Test succeeded\n";
echo "----------------\n";
foreach ($arrObjects as $object)
{
	echo $object->getName() . "\n";
	echo $object->getBody("de") . "\n";
	echo "Meine Eltern heißen " . $object->getParent()->getName() . "\n";
	echo "Validate Field 'Name': " . $object->validateName("Carmen") . "\n";
	echo "Validate Field 'Body': " . $object->validateBody("Carmen") . "\n";
	echo "Static Value: " . $object->getParent()->getInfo($langA) . "\n";
	echo "GeoLocation: " . $arrGeoLocation['x'] . ', ' . $arrGeoLocation['y'] . "\n";
	echo "Option references: " . implode(', ', $entityType->getFieldOptionReferences('name', $langA)) . "\n";
	echo "\n";
}
