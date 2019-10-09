<?php
$update_version = 0.13;

$sql = [
];

$inserts = [
    'insert into perms values (:id,:name)'=>[
        [':id'=>uniqid(),':name'=>'viewLostTime']
    ]
];