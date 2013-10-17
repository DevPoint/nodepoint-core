<?php

header("Content-Type:text/plain; charset=utf-8");

use TinyCms\NodeProvider\Library\MagicFieldCallInfo;
use TinyCms\NodeProvider\Type\Entity\Entity;
use TinyCms\NodeProvider\Type\Node\Node;
use TinyCms\NodeProvider\Type\Position2d\Position2d;

// establish connection to database
$config = new \Doctrine\DBAL\Configuration();
$connectionParams = array(
    'dbname' => 'tinycms',
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'driver' => 'pdo_mysql'
);
$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

// construct parameters
$params = array();
$ids = array(1,2);
$idsStrIin = str_repeat('?,', count($ids) - 1) . '?';
$params = array_merge($params, $ids);// add array of id
//$params[] = 'TinyCmsCore/User'; // add type

// execute query
$sql = "SELECT * FROM tcm_entities WHERE id IN({$idsStrIin})";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();
print_r($users);


