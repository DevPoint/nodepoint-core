<?php

header("Content-Type:text/plain; charset=utf-8");

use TinyCms\NodeProvider\Library\MagicFieldCallInfo;
use TinyCms\NodeProvider\Type\Entity\Entity;
use TinyCms\NodeProvider\Type\Node\Node;
use TinyCms\NodeProvider\Type\Position2d\Position2d;

// establish connection to database
$dbuser = 'root';
$dbpass = '';
$conn = new \PDO('mysql:host=localhost;dbname=tinycms', $dbuser, $dbpass);

// construct parameters
$params = array();
$ids = array(1,2,3);
$idsStrIin = str_repeat('?,', count($ids) - 1) . '?';
$params = array_merge($params, $ids);// add array of id
//$params[] = 'TinyCmsCore/User'; // add type

// execute query
$sql = "SELECT * FROM tcm_entities WHERE id IN({$idsStrIin})";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($users);

$conn = null;
