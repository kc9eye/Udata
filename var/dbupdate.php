<?php
$update_version = 0.16;

$sql = [
    'alter table cell_tooling add column torque_val varchar, add column torque_units varchar, add column torque_label varchar'
];

$inserts = [
    'insert into tools (id,description,category,uid,_date) values (:id,:description,:category,:uid,now())'=>[
        [
            ':id'=>uniqid(),
            ':description'=>'Unique Torque Wrench',
            ':category'=>'torque wrench',
            ':uid'=>'123456789'
        ]
    ]
];