<?php

header("Content-Type:text/plain; charset=utf-8");

use TinyCms\NodeProvider\Library\MagicFieldCallInfo;
use TinyCms\NodeProvider\Type\Entity\EntityType;
use TinyCms\NodeProvider\Type\Entity\Entity;
use TinyCms\NodeProvider\Type\Node\Node;
use TinyCms\NodeProvider\Type\Document\Document;

// establish connection to database
$dbuser = 'root';
$dbpass = '';
$conn = new PDO('mysql:host=localhost;dbname=tinycms', $dbuser, $dbpass);
$em = new \TinyCms\NodeProvider\Storage\PDO\Library\EntityManager($conn);

// create types
$integerType = new \TinyCms\NodeProvider\Type\Integer\IntegerType();
$aliasType = new \TinyCms\NodeProvider\Type\Alias\AliasType();
$stringType = new \TinyCms\NodeProvider\Type\String\StringType();

$nodeType = new \TinyCms\NodeProvider\Type\Node\NodeType();
$nodeType->setFieldType('id', $integerType);
$nodeType->setFieldStorageDesc('id', array('type'=>EntityType::STORAGE_INT));
$nodeType->setFieldType('alias', $aliasType);
$nodeType->setFieldType('name', $stringType);
$nodeType->setFieldDescription('name', array('i18n'=>true));
$nodeType->finalize();

$documentType = new \TinyCms\NodeProvider\Type\Document\DocumentType();
$documentType->setFieldType('id', $integerType);
$documentType->setFieldStorageDesc('id', array('type'=>EntityType::STORAGE_INT));
$documentType->setFieldType('alias', $aliasType);
$documentType->setFieldType('parent', $nodeType);
$documentType->setFieldType('name', $stringType);
$documentType->setFieldDescription('name', array('i18n'=>true));
$documentType->setFieldType('body', $stringType);
$documentType->setFieldDescription('body', array('i18n'=>true));
$documentType->finalize();

// language codes
$langA = "de";
$langB = "en";

// create object instance
$parent = new Node($nodeType);
$parent->setAlias("root");
$parent->setName($langA, "Root");
$em->persist($parent);

$arrObjects = array();
$object = new Document($documentType);
$object->setParent($parent);
$object->setAlias("julian-brabsche");
$object->setName($langA, "Julian Brabsche");
$object->setBody($langA, "Hier kommt Julian, unser Mathe-Genie!");
$arrObjects[] = $object;
$em->persist($object);

$object->setName($langA, "J. Brabsche");

$object = new Document($documentType);
$object->setParent($parent);
$object->setAlias("david-brabsche");
$object->setName($langA, "David Brabsche");
$object->setBody($langA, "Hier kommt unser lieber David!");
$arrObjects[] = $object;
$em->persist($object);

$em->flush();

// output test result
echo "Test succeeded\n";
echo "----------------\n";
echo $documentType->getFieldStorageType('name') . "\n";

$conn = null;
