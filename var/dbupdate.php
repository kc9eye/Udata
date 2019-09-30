<?php
$update_version = 0.12;

$sql = [
    'ALTER TABLE public.profiles
    ADD COLUMN theme character varying'
];

$inserts = [
];