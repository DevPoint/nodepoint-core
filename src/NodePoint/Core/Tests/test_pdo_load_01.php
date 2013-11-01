<?php

header("Content-Type:text/plain; charset=utf-8");

use NodePoint\Core\Library\MagicFieldCallInfo;
use NodePoint\Core\Library\TypeInterface;
use NodePoint\Core\Type\Position2d\Position2d;
use NodePoint\Core\Type\Entity\EntityType;
use NodePoint\Core\Type\Entity\Entity;
use NodePoint\Core\Type\Node\Node;
use NodePoint\Core\Type\Document\Document;

// register primitive types
$typeFactory = new \NodePoint\Core\Library\TypeFactory();
$typeFactory->registerTypeClass('NodePointCore/Integer', "\\NodePoint\\Core\\Type\\Integer\\IntegerType");
$typeFactory->registerTypeClass('NodePointCore/Alias', "\\NodePoint\\Core\\Type\\Alias\\AliasType");
$typeFactory->registerTypeClass('NodePointCore/String', "\\NodePoint\\Core\\Type\\String\\StringType");
$typeFactory->registerTypeClass('NodePointCore/Text', "\\NodePoint\\Core\\Type\\Text\\TextType");
$typeFactory->registerTypeClass('NodePointCore/Position2d', "\\NodePoint\\Core\\Type\\Position2d\\Position2dType");

// establish connection to database
$dbuser = 'root';
$dbpass = '';
$conn = new PDO('mysql:host=localhost;dbname=nodepoint', $dbuser, $dbpass);
$em = new \NodePoint\Core\Storage\PDO\Library\EntityManager($conn, $typeFactory);

// repository class names
$nodeRepositoryClass = "\\NodePoint\\Core\\Storage\\PDO\\Type\\Node\\NodeRepository";

// create entity types
$stringType = $typeFactory->getType('NodePointCore/String');
$position2dType = $typeFactory->getType('NodePointCore/Position2d');

$nodeType = new \NodePoint\Core\Type\Node\NodeType($typeFactory, true);
$nodeType->setFieldType('name', $stringType);
$nodeType->setFieldDescription('name', array('i18n'=>true));
$nodeType->finalize();
$typeFactory->registerType($nodeType);
$em->registerRepositoryClass($nodeType->getTypeName(), $nodeRepositoryClass);

$documentType = new \NodePoint\Core\Type\Document\DocumentType($typeFactory, true);
$documentType->setFieldType('name', $stringType);
$documentType->setFieldDescription('name', array('i18n'=>true));
$documentType->setFieldType('geolocation', $position2dType);
$documentType->setFieldType('body', $stringType);
$documentType->setFieldDescription('body', array('i18n'=>true));
$documentType->finalize();
$typeFactory->registerType($documentType);
$em->registerRepositoryClass($documentType->getTypeName(), $nodeRepositoryClass);

// language codes
$langA = "de";
$langB = "en";
$objects = array();

$object = $em->find('NodePointCore/Node', 2);
$objects[] = $object;

$object = $em->find('NodePointCore/Node', 3);
$objects[] = $object;


$em->flush();


// output test result
echo "Test succeeded\n";
echo "----------------\n";
foreach ($objects as $object)
{
	echo sprintf("Parent: %s\n", $object->getParentId());
	echo sprintf("Alias: %s\n", $object->getAlias($langA));
	echo sprintf("Name: %s\n", $object->getName($langA));
	echo sprintf("Body: %s\n", $object->getBody($langA));
	$geolocation = $object->getGeolocation();
	if (null !== $geolocation)
	{
		echo sprintf("Geolocation: %0.3f, %0.3f\n", $geolocation->x, $geolocation->y);
	}

	echo "\n";
}

// clear connection
$conn = null;
