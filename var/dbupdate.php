<?php
$update_version = 0.1;

$sql = [
    'create table review_comments (
        id varchar primary key,
        uid varchar not null,
        revid varchar not null,
        comments text not null,
        _date timestamp with time zone default now()
    )',
    'create table reviews (
        id varchar primary key,
        eid varchar not null,
        start_date timestamp with time zone default now(),
        end_date timestamp with time zone default (now() + \'7 days\'::interval),
        meeting_date date,
        uid varchar not null
    )'
];

$inserts = [
    'insert into perms values (:id,:name)'=>
    [
        [
            ':id'=>uniqid(),
            ':name'=>'initEmployeeReview'
        ],
        [
            ':id'=>uniqid(),
            ':name'=>'reviewEmployee'
        ]
    ],
    "insert into notifications values (:id,:description,'EMAIL',now(),'5bc4c8a517238')"=>[
        [
            ':id'=>uniqid(),
            ':description'=>'Review Started'
        ]
    ]    
];