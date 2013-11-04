<?php

header("Content-Type:text/plain; charset=utf-8");

use NodePoint\Core\Type\Entity\Entity;
use NodePoint\Core\Type\Node\Node;
use NodePoint\Core\Type\Position2d\Position2d;

// register primitive types
$typeFactory = new \NodePoint\Core\Library\TypeFactory();
$typeFactory->registerTypeClass('NodePointCore/Integer', "\\NodePoint\\Core\\Type\\Integer\\IntegerType");
$typeFactory->registerTypeClass('NodePointCore/Alias', "\\NodePoint\\Core\\Type\\Alias\\AliasType");
$typeFactory->registerTypeClass('NodePointCore/String', "\\NodePoint\\Core\\Type\\String\\StringType");
$typeFactory->registerTypeClass('NodePointCore/Text', "\\NodePoint\\Core\\Type\\Text\\TextType");
$typeFactory->registerTypeClass('NodePointCore/Position2d', "\\NodePoint\\Core\\Type\\Position2d\\Position2dType");

// language codes
$langA = "de";
$langB = "en";

// create entity types
$stringType = $typeFactory->getType('NodePointCore/String');
$position2dType = $typeFactory->getType('NodePointCore/Position2d');

$nodeType = new \NodePoint\Core\Type\Node\NodeType($typeFactory, false);
$nodeType->setFieldType('name', $stringType);
$nodeType->setFieldDescription('name', array('hasOptions'=>true,'options'=>array('wilfried','carmen','david','julian','milena')));
$nodeType->setFieldType('body', $stringType);
$nodeType->setFieldDescription('body', array('i18n'=>true));
$nodeType->setFieldType('geolocation', $position2dType);
$nodeType->setFieldType('info', $stringType);
$nodeType->setFieldDescription('info', array('static'=>true, 'i18n'=>true));
$nodeType->finalize();
$typeFactory->registerType($nodeType);

// set static values
$entityStatic = $nodeType->getStaticEntity();
$entityStatic->setInfo($langA, "Informationsunterlagen");
$entityStatic->setInfo($langB, "Information material");

// create object instance
$parent = new Node($nodeType);
$parent->setAlias("carmen-und-wilfried");
$parent->setName("Carmen und Wilfried");
$geolocation = new Position2d();
$geolocation->set(41.501, 14.502);
$parent->setGeolocation($geolocation);

$arrObjects = array();
$object = new Node($nodeType);
$object->setParent($parent);
$object->setAlias("julian-brabsche");
$object->setName("Julian Brabsche");
$object->setBody($langA, "Hier kommt Julian, unser Mathe-Genie!");
$object->setBody($langB, "Here comes Julian, our mathematics genious!");
$geolocation = new Position2d();
$geolocation->set(43.001, 15.002);
$object->setGeolocation($geolocation);
$arrObjects[] = $object;

$object = new Node($nodeType);
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
	echo "Mein Zugriffsname: " . $object->getAlias() . "\n";
	echo "Meine Eltern heißen " . $object->getParent()->getName() . "\n";
	echo "Validate Field 'Name': " . $object->validateName("Carmen") . "\n";
	echo "Validate Field 'Body': " . $object->validateBody("Carmen") . "\n";
	echo "Static Value: " . $object->getInfo($langOut) . "\n";
	echo "Du findest mich an folgenden Geokoordination: " . $arrGeolocation['x'] . ', ' . $arrGeolocation['y'] . "\n";
	echo "Name Options: " . implode(', ', $object->_type()->getFieldInfo('name')->getOptions()) . "\n";
	echo "\n";
}
