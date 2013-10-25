<?php

header("Content-Type:text/plain; charset=utf-8");

use NodePoint\Core\Library\MagicFieldCallInfo;
use NodePoint\Core\Type\Entity\Entity;
use NodePoint\Core\Type\Node\Node;
use NodePoint\Core\Type\Position2d\Position2d;

// establish connection to database
$dbuser = 'root';
$dbpass = '';
$conn = new \PDO('mysql:host=localhost;dbname=tinycms', $dbuser, $dbpass);

// construct parameters
$params = array();
$ids = array(1,2,3);
$idsStrIin = str_repeat('?,', count($ids) - 1) . '?';
$params = array_merge($params, $ids);// add array of id
//$params[] = 'NodePointCore/User'; // add type

// execute query
$sql = "SELECT * FROM tcm_entities WHERE id IN({$idsStrIin})";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($users);

$conn = null;
