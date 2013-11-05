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

// create node type
$stringType = $typeFactory->getType('NodePointCore/String');
$position2dType = $typeFactory->getType('NodePointCore/Position2d');

$nodeType = new \NodePoint\Core\Type\Node\NodeType($typeFactory, true);
$nodeType->setFieldInfo('name', $stringType, array('i18n'=>true));
$nodeType->finalize();
$typeFactory->registerType($nodeType);
$em->registerRepositoryClass($nodeType->getTypeName(), $nodeRepositoryClass);

// create user type
$userType = new \NodePoint\Core\Type\User\UserType($typeFactory, true);
$userType->finalize();
$typeFactory->registerType($userType);
$em->registerRepositoryClass($userType->getTypeName(), $nodeRepositoryClass);

// create document type
$documentType = new \NodePoint\Core\Type\Document\DocumentType($typeFactory, true);
$documentType->setFieldInfo('author', $userType);
$documentType->setFieldInfo('geolocation', $position2dType);
$documentType->setFieldInfo('body', $stringType, array('i18n'=>true));
$documentType->finalize();
$typeFactory->registerType($documentType);
$em->registerRepositoryClass($documentType->getTypeName(), $nodeRepositoryClass);

// language codes
$langA = "de";
$langB = "en";

// create object instance
$parent = new Node($nodeType);
$parent->setAlias($langA, "root");
$parent->setName($langA, "Root");
$em->persist($parent);

$userA = new User($userType);
$userA->setAlias($langA, "wilfried");
$userA->setName($langA, "Wilfried Reiter");
$em->persist($userA);

$userB = new User($userType);
$userB->setAlias($langA, "carmen");
$userB->setName($langA, "Carmen Brabsche");
$em->persist($userB);

$arrObjects = array();
$object = new Document($documentType);
$object->setParent($parent);
$object->setAuthor($userA);
$object->setAlias($langA, "julian-brabsche");
$object->setName($langA, "Julian Brabsche");
$object->setBody($langA, "Hier kommt Julian, unser Mathe-Genie!");
$object->setBody($langB, "Here comes Julian, our Mathe-Genius!");
$geolocation = new Position2d();
$geolocation->set(43.001, 15.002);
$object->setGeolocation($geolocation);
$arrObjects[] = $object;
$em->persist($object);

$object->setName($langA, "J. Brabsche");

$object = new Document($documentType);
$object->setParent($parent);
$object->setAuthor($userB);
$object->setAlias($langA, "david-brabsche");
$object->setName($langA, "David Brabsche");
$object->setBody($langA, "Hier kommt unser lieber David!");
$object->setBody($langB, "Here comes our cute David!");
$arrObjects[] = $object;
$em->persist($object);

$em->flush();

// output test result
echo "Test succeeded\n";
echo "----------------\n";

// clear connection
$conn = null;
