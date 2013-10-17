<?php

header("Content-Type:text/plain; charset=utf-8");

use TinyCms\NodeProvider\Library\MagicFieldCallInfo;
use TinyCms\NodeProvider\Type\Entity\Entity;
use TinyCms\NodeProvider\Type\Node\Node;
use TinyCms\NodeProvider\Type\Document\Document;

// establish connection to database
$dbuser = 'root';
$dbpass = '';
$conn = new PDO('mysql:host=localhost;dbname=tinycms', $dbuser, $dbpass);

// create types
$parentType = new TinyCms\NodeProvider\Type\Node\NodeType();
$integerType = new TinyCms\NodeProvider\Type\Integer\IntegerType();
$aliasType = new TinyCms\NodeProvider\Type\Alias\AliasType();
$stringType = new TinyCms\NodeProvider\Type\String\StringType();
$entityType = new TinyCms\NodeProvider\Type\Document\DocumentType();
$entityType->setFieldType('id', $integerType);
$entityType->setFieldDescription('id', array('isPrimary'=>true));
$entityType->setFieldType('parent', $parentType);
$entityType->setFieldType('alias', $aliasType);
$entityType->setFieldType('name', $stringType);
$entityType->setFieldDescription('name', array('i18n'=>true));
$entityType->setFieldType('body', $stringType);
$entityType->setFieldDescription('body', array('i18n'=>true));
$entityType->setMagicFieldCallInfo('setId', new MagicFieldCallInfo('id', '_setMagicFieldCall'));
$entityType->setMagicFieldCallInfo('getId', new MagicFieldCallInfo('id', '_getMagicFieldCall'));
$entityType->setMagicFieldCallInfo('setParent', new MagicFieldCallInfo('parent', '_setMagicFieldCall'));
$entityType->setMagicFieldCallInfo('getParent', new MagicFieldCallInfo('parent', '_getMagicFieldCall'));
$entityType->setMagicFieldCallInfo('setAlias', new MagicFieldCallInfo('alias', '_setMagicFieldCall'));
$entityType->setMagicFieldCallInfo('getAlias', new MagicFieldCallInfo('alias', '_getMagicFieldCall'));
$entityType->setMagicFieldCallInfo('setName', new MagicFieldCallInfo('name', '_setMagicFieldCallI18n'));
$entityType->setMagicFieldCallInfo('getName', new MagicFieldCallInfo('name', '_getMagicFieldCallI18n'));
$entityType->setMagicFieldCallInfo('setBody', new MagicFieldCallInfo('body', '_setMagicFieldCallI18n'));
$entityType->setMagicFieldCallInfo('getBody', new MagicFieldCallInfo('body', '_getMagicFieldCallI18n'));

// language codes
$langA = "de";
$langB = "en";

// create object instance
$parent = new Node($parentType);
$parent->setAlias("root");
$parent->setName("Root");

$arrObjects = array();
$object = new Document($entityType);
$object->setParent($parent);
$object->setAlias("julian-brabsche");
$object->setName($langA, "Julian Brabsche");
$object->setBody($langA, "Hier kommt Julian, unser Mathe-Genie!");
$arrObjects[] = $object;

$object = new Node($entityType);
$object->setParent($parent);
$object->setAlias("david-brabsche");
$object->setName($langA, "David Brabsche");
$object->setBody($langA, "Hier kommt unser lieber David!");
$arrObjects[] = $object;

// output test result
echo "Test succeeded\n";
echo "----------------\n";

$conn = null;
