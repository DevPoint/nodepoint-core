<?php

header("Content-Type:text/plain; charset=utf-8");

use NodePoint\Core\Library\MagicFieldCallInfo;
use NodePoint\Core\Library\TypeInterface;
use NodePoint\Core\Type\Position2d\Position2d;
use NodePoint\Core\Type\Entity\EntityType;
use NodePoint\Core\Type\Entity\Entity;
use NodePoint\Core\Type\Node\Node;
use NodePoint\Core\Type\Folder\Folder;
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
$conn = new PDO('mysql:host=localhost;dbname=nodepoint', $dbuser, $dbpass);
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
$langB = "en";

// create object instance
$parent = new Folder($folderType);
$parent->setName($langA, "Root");
$em->persist($parent);

$userA = new User($userType);
$userA->setAlias("wilfried");
$userA->setName($langA, "Wilfried Reiter");
$userA->setEmail("wilfried@creativity4me.com");
$em->persist($userA);

$userB = new User($userType);
$userB->setName($langA, "Carmen Brabsche");
$userB->setAlias("carmen");
$userB->setEmail("carmen.1978@gmx.at");
$em->persist($userB);

$arrObjects = array();
$object = new Document($documentType);
$object->setParent($parent);
$object->setAuthor($userA);
$object->setAlias($langA, "julian-brabsche");
$object->setName($langA, "Julian Brabsche");
$object->setBody($langA, "Hier kommt Julian, unser Mathe-Genie!");
$object->setBody($langB, "Here comes Julian, our Mathe-Genius!");
$object->setWeight('17.8');
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
$object->setWeight('14.333');
$arrObjects[] = $object;
$em->persist($object);

$em->flush();

// output test result
echo "Test succeeded\n";
echo "----------------\n";

// clear connection
$conn = null;
