<?php

header("Content-Type:text/plain; charset=utf-8");

use NodePoint\Core\Library\MagicFieldCallInfo;
use NodePoint\Core\Library\TypeInterface;
use NodePoint\Core\Type\Position2d\Position2d;
use NodePoint\Core\Type\Entity\EntityType;
use NodePoint\Core\Type\Entity\Entity;
use NodePoint\Core\Type\Node\Node;
use NodePoint\Core\Type\Document\Document;

// establish connection to database
$dbuser = 'root';
$dbpass = '';
$conn = new PDO('mysql:host=localhost;dbname=nodepoint', $dbuser, $dbpass);
$em = new \NodePoint\Core\Storage\PDO\Library\EntityManager($conn);

// create types
$integerType = new \NodePoint\Core\Type\Integer\IntegerType();
$aliasType = new \NodePoint\Core\Type\Alias\AliasType();
$stringType = new \NodePoint\Core\Type\String\StringType();
$position2dType = new \NodePoint\Core\Type\Position2d\Position2dType();

$nodeType = new \NodePoint\Core\Type\Node\NodeType();
$nodeType->setFieldType('id', $integerType);
$nodeType->setFieldType('parent', $nodeType);
$nodeType->setFieldType('parentField', $stringType);
$nodeType->setFieldType('alias', $aliasType);
$nodeType->setFieldDescription('alias', array('i18n'=>true, 'searchable'=>true));
$nodeType->setFieldType('name', $stringType);
$nodeType->setFieldDescription('name', array('i18n'=>true));
$nodeType->finalize();

$documentType = new \NodePoint\Core\Type\Document\DocumentType();
$documentType->setFieldType('id', $integerType);
$documentType->setFieldType('parent', $nodeType);
$documentType->setFieldType('parentField', $stringType);
$documentType->setFieldType('alias', $aliasType);
$documentType->setFieldDescription('alias', array('searchable'=>true));
$documentType->setFieldType('name', $stringType);
$documentType->setFieldType('geolocation', $position2dType);
$documentType->setFieldDescription('name', array('i18n'=>true));
$documentType->setFieldType('body', $stringType);
$documentType->setFieldDescription('body', array('i18n'=>true));
$documentType->finalize();

// language codes
$langA = "de";
$langB = "en";

// create object instance
$parent = new Node($nodeType);
$parent->setAlias($langA, "root");
$parent->setName($langA, "Root");
$em->persist($parent);

$arrObjects = array();
$object = new Document($documentType);
$object->setParent($parent);
$object->setAlias("julian-brabsche");
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
$object->setAlias("david-brabsche");
$object->setName($langA, "David Brabsche");
$object->setBody($langA, "Hier kommt unser lieber David!");
$object->setBody($langB, "Here comes our cute David!");
$arrObjects[] = $object;
$em->persist($object);

$em->flush();

// output test result
echo "Test succeeded\n";
echo "----------------\n";
echo $documentType->getFieldStorageType('parent') . "\n";

$conn = null;
