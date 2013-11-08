<?php

header("Content-Type:text/plain; charset=utf-8");

use NodePoint\Core\Type\Entity\Entity;
use NodePoint\Core\Type\Node\Node;
use NodePoint\Core\Type\Position2d\Position2d;

// register primitive types
$typeFactory = new \NodePoint\Core\Library\TypeFactory();
$typeFactory->registerTypeClass('Core/Integer', "\\NodePoint\\Core\\Type\\Integer\\IntegerType");
$typeFactory->registerTypeClass('Core/Number', "\\NodePoint\\Core\\Type\\Number\\NumberType");
$typeFactory->registerTypeClass('Core/Alias', "\\NodePoint\\Core\\Type\\Alias\\AliasType");
$typeFactory->registerTypeClass('Core/String', "\\NodePoint\\Core\\Type\\String\\StringType");
$typeFactory->registerTypeClass('Core/Text', "\\NodePoint\\Core\\Type\\Text\\TextType");
$typeFactory->registerTypeClass('Core/Position2d', "\\NodePoint\\Core\\Type\\Position2d\\Position2dType");

// language codes
$langA = "de";
$langB = "en";

// get primitive types
$integerType = $typeFactory->getType('Core/Integer');
$numberType = $typeFactory->getType('Core/Number');
$stringType = $typeFactory->getType('Core/String');
$position2dType = $typeFactory->getType('Core/Position2d');
$aliasType = $typeFactory->getType('Core/Alias');

// create node type
$nodeType = new \NodePoint\Core\Type\Node\NodeType($typeFactory, false);
$nodeType->setFieldInfo('alias', $aliasType)
				->setDescription(array('searchable'=>true, 'alias'=>'_alias'));
$nodeType->setFieldInfo('name', $stringType)
				->setDescription(array('hasOptions'=>true, 'options'=>array('wilfried','carmen','david','julian','milena')))
				->setRules(array('minLength'=>3,'maxLength'=>32));
$nodeType->setFieldInfo('body', $stringType, array('i18n'=>true));
$nodeType->setFieldInfo('geolocation', $position2dType);
$nodeType->setFieldInfo('weight', $numberType)
				->setRules(array('maxValue'=>'300.0'));
$nodeType->finalize();
$typeFactory->registerType($nodeType);

// create object instance
$parent = new Node($nodeType);
$parent->setAlias("carmen-und-wilfried");
$parent->setName("Carmen und Wilfried");
$geolocation = new Position2d();
$geolocation->set(41.501, 14.502);
$parent->setGeolocation($geolocation);

$validateErrors = array();
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
$object->setWeight(17.81);
$arrObjects[] = $object;

$object = new Node($nodeType);
$object->setParent($parent);
$object->setAlias("david-brabsche");
$object->setName("David Brabsche");
$object->setBody($langA, "Hier kommt unser lieber David!");
$object->setBody($langB, "Here comes our cute David!");
$geolocation = new Position2d();
$geolocation->set(43.351, 15.203);
$object->setGeolocation($geolocation);
$errors = $object->validateWeight('15.4');
if (true !== $errors)
{
	$validateErrors['weight'] = $errors;
}
$arrObjects[] = $object;

// output test result
echo "Test succeeded\n";
echo "----------------\n";
foreach ($arrObjects as $object)
{
	$langOut = $langB;
	echo $object->getName() . "\n";
	echo $object->getBody($langOut) . "\n";
	echo "Zugriffsname: " . $object->getAlias() . "\n";
	echo "Meine Eltern heißen " . $object->getParent()->getName() . "\n";
	echo "Ich wiege " . $object->getWeight() . "kg\n";
	$arrGeolocation = $object->_fieldType('geolocation')->objectToArray($object->getGeolocation());
	echo "Du findest mich an folgenden Geokoordination: " . $arrGeolocation['x'] . ', ' . $arrGeolocation['y'] . "\n";
	echo "\n";
}
if (!empty($validateErrors))
{
	foreach ($validateErrors as $field => $arrError)
	{
		foreach ($arrError as $error)
		{
			printf("Error Validate '%s': %s\n", $field, $error);
		}
	}
	echo "\n";
}
