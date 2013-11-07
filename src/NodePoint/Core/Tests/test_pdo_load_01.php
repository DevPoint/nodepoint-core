<?php

header("Content-Type:text/plain; charset=utf-8");

use NodePoint\Core\Library\MagicFieldCallInfo;
use NodePoint\Core\Library\TypeInterface;
use NodePoint\Core\Type\Position2d\Position2d;
use NodePoint\Core\Type\Entity\EntityType;
use NodePoint\Core\Type\Entity\Entity;
use NodePoint\Core\Type\Node\Node;
use NodePoint\Core\Type\Document\Document;
use NodePoint\Core\Type\User\User;

// register primitive types
$typeFactory = new \NodePoint\Core\Library\TypeFactory();
$typeFactory->registerTypeClass('NodePointCore/Integer', "\\NodePoint\\Core\\Type\\Integer\\IntegerType");
$typeFactory->registerTypeClass('NodePointCore/Number', "\\NodePoint\\Core\\Type\\Number\\NumberType");
$typeFactory->registerTypeClass('NodePointCore/Alias', "\\NodePoint\\Core\\Type\\Alias\\AliasType");
$typeFactory->registerTypeClass('NodePointCore/String', "\\NodePoint\\Core\\Type\\String\\StringType");
$typeFactory->registerTypeClass('NodePointCore/Text', "\\NodePoint\\Core\\Type\\Text\\TextType");
$typeFactory->registerTypeClass('NodePointCore/Email', "\\NodePoint\\Core\\Type\\Email\\EmailType");
$typeFactory->registerTypeClass('NodePointCore/Position2d', "\\NodePoint\\Core\\Type\\Position2d\\Position2dType");

// establish connection to database
$dbuser = 'root';
$dbpass = '';
$conn = new PDO('mysql:host=localhost;dbname=nodepoint;charset=utf8', $dbuser, $dbpass);
$em = new \NodePoint\Core\Storage\PDO\Library\EntityManager($conn, $typeFactory);

// repository class names
$nodeRepositoryClass = "\\NodePoint\\Core\\Storage\\PDO\\Type\\Node\\NodeRepository";

// get primitive types
$numberType = $typeFactory->getType('NodePointCore/Number');
$stringType = $typeFactory->getType('NodePointCore/String');
$position2dType = $typeFactory->getType('NodePointCore/Position2d');
$aliasType = $typeFactory->getType('NodePointCore/Alias');

// create node type
$nodeType = new \NodePoint\Core\Type\Node\NodeType($typeFactory, true);
$nodeType->finalize();
$typeFactory->registerType($nodeType);
$em->registerRepositoryClass($nodeType->getTypeName(), $nodeRepositoryClass);

// create folder type
$folderType = new \NodePoint\Core\Type\Folder\FolderType($typeFactory, true);
$folderType->setFieldInfo('name', $stringType, array('searchable'=>true, 'i18n'=>true));
$folderType->finalize();
$typeFactory->registerType($folderType);
$em->registerRepositoryClass($folderType->getTypeName(), $nodeRepositoryClass);

// create user type
$userType = new \NodePoint\Core\Type\User\UserType($typeFactory, true);
$userType->setFieldInfo('name', $stringType);
$userType->finalize();
$typeFactory->registerType($userType);
$em->registerRepositoryClass($userType->getTypeName(), $nodeRepositoryClass);

// create document type
$documentType = new \NodePoint\Core\Type\Document\DocumentType($typeFactory, true);
$documentType->setFieldInfo('name', $stringType, array('i18n'=>true));
$documentType->setFieldInfo('author', $userType);
$documentType->setFieldInfo('weight', $numberType)
					->setDescription(array('searchable'=>true))
					->setRules(array('maxValue'=>999));
$documentType->setFieldInfo('geolocation', $position2dType);
$documentType->setFieldInfo('body', $stringType, array('i18n'=>true));
$documentType->finalize();
$typeFactory->registerType($documentType);
$em->registerRepositoryClass($documentType->getTypeName(), $nodeRepositoryClass);

// language codes
$langA = "de";
$langB = "fr";
$objects = array();

$object = $em->find('NodePointCore/Document', 4);
$object->setName($langA, 'Julian Brabsche');
$object->setName($langB, 'Juliene Brabsché');
$objects[] = $object;

$object = $em->find('NodePointCore/Document', 5);
$objects[] = $object;

$em->flush();


// output test result
echo "Test succeeded\n";
echo "----------------\n";
foreach ($objects as $object)
{
	echo sprintf("Parent: %s\n", $object->getParent()->getName($langA));
	echo sprintf("Author: %s\n", $object->getAuthor()->getEmail());
	echo sprintf("Alias: %s\n", $object->getAlias($langA));
	echo sprintf("Name: %s\n", $object->getName($langA));
	echo sprintf("Body: %s\n", $object->getBody($langA));
	$geolocation = $object->getGeolocation();
	if (null !== $geolocation)
	{
		echo sprintf("Geolocation: %0.3f, %0.3f\n", $geolocation->x, $geolocation->y);
	}
	$weight = $object->getWeight();
	if (null !== $weight)
	{
		echo sprintf("Gewicht: %0.1f\n", $weight);
	}

	echo "\n";
}

// clear connection
$conn = null;
