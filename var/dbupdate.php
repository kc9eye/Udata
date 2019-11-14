<?php
$update_version = 0.17;

$sql = [
    'alter table auth_tokens add column host inet'
];

$inserts = [
];