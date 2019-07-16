<?php
/* This file is part of UData.
 * Copyright (C) 2018 Paul W. Lane <kc9eye@outlook.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */
require_once(dirname(__DIR__).'/lib/init.php');
include('safetymigrationobjects.php');
$server->userMustHaveRole('Administrator');

#DIsable the time limit for large files
ignore_user_abort(true);
set_time_limit(0);
$olddb = new PDO('pgsql:host=localhost;port=5432;dbname=447001;sslmode=prefer','udata','MonkeyFuck34');

#Output useful info
echo "<html><head><title>DB Hacks</title></head><body><h1>Hacking Database</h1><pre>\n";
/*
echo "Creating Database Objects.... \n";
try {
    foreach($objs as $object) {
        if ($server->pdo->query($object) === false) {
            throw new Exception('Query failed');
        }
        else {
            echo "Created object with: {$object}\n";
        }
    }
    echo "done\n";
}
catch (Exception $e) {
    die($e->getMessage());
}

#Start converting to new DB system
$cnt = 0;
echo "Converting Old MSDS to new SDS... ";

$msds = $olddb->query('SELECT * FROM msds');

$sds = $server->pdo->prepare('INSERT INTO sds VALUES (:id,:name,:used,:dist,now(),:uid,:meta)');
$index = $server->pdo->prepare('INSERT INTO file_index VALUES (:id,:file,:mime,:ref,:ref_id,:orig,now(),:uid)');

$server->pdo->beginTransaction();
try {
    while (($row = $msds->fetch(PDO::FETCH_ASSOC))) {
        $sds_insert = [
            ':id'=>$row['id'],
            ':name'=>$row['p_name'],
            ':used'=>$row['loc'],
            ':dist'=>$row['distributor'],
            ':uid'=>'migration-program',
            ':meta'=>$row['keywords']
        ];
        $index_insert = [
            ':id'=>uniqid(),
            ':file'=>basename($row['f_name']).'.pdf',
            ':mime'=>(is_null($row['f_mime']) ? mime_content_type($row['f_name']) : $row['f_mime']),
            ':ref'=>\SDS_FILE_REF,
            ':ref_id'=>$row['id'],
            ':orig'=>(is_null($row['orig_name']) ? '?' : $row['orig_name']),
            ':uid'=>'migration-program'
        ];
        $sds->execute($sds_insert);
        $index->execute($index_insert);
        $cnt++;

        #Move the file to the testing data-root and rename it
        if (!copy($row['f_name'],$server->config['data-root'].'/'.$index_insert[':file'])) {
            throw new Exception("Failed to move file: {$row['f_name']}");
        }
    }
    $server->pdo->commit();
    echo "{$cnt} records converted\n";
}
catch (Exception $e) {
    $server->pdo->rollBack();
    die($e->getMessage());
}
*/

echo 'Converting old Meeing Minutes to new... ';
$cnt = 0;

#Convert safety meeting minutes
$minutes = $olddb->query('SELECT * FROM safety_minutes');
$sbm = $server->pdo->prepare('INSERT INTO sbm VALUES (:id,:date,:uid)');
$sbm_index = $server->pdo->prepare('INSERT INTO file_index VALUES (:id,:file,:mime,:ref,:ref_id,:orig,now(),:uid)');
$server->pdo->beginTransaction();
try{
    while (($row = $minutes->fetch(PDO::FETCH_ASSOC))) {
        switch($row['f_mime']) {
            case 'application/pdf': $ext = '.pdf'; break;
            case 'application/msword': $ext = '.doc'; break;
        }
        switch($row['month']) {
            case 'JANUARY': $month = '1'; break;
            case 'FEBRUARY': $month = '2'; break;
            case 'MARCH': $month = '3'; break;
            case 'APRIL': $month = '4'; break;
            case 'MAY': $month = '5'; break;
            case 'JUNE': $month = '6'; break;
            case 'JULY': $month = '7'; break;
            case 'AUGUST': $month = '8'; break;
            case 'SEPTEMBER': $month = '9'; break;
            case 'OCTOBER': $month = '10'; break;
            case 'NOVEMBER': $month = '11'; break;
            case 'DECEMBER': $month = '12'; break;
        }
        $sbm_insert = [
            ':id'=>$row['id'],
            ':date'=>$row['year'].'/'.$month.'/15',
            ':uid'=>'migration-program'
        ];
        $sbm_index_insert = [
            ':id'=>uniqid(),
            ':file'=>basename($row['f_name']).$ext,
            ':mime'=>$row['f_mime'],
            ':ref'=>\SBM_FILE_REF,
            ':ref_id'=>$sbm_insert[':id'],
            ':orig'=>(is_null($row['orig_name']) ? '?' : $row['orig_name']),
            ':uid'=>'migration-program'
        ];

        if (!$sbm->execute($sbm_insert)) {
            throw new Exception("Insert failed: ".print_r($sbm_insert, true));
        }
        if (!$sbm_index->execute($sbm_index_insert)) {
            throw new Exception("Insert failed: ".print_r($sbm_index_insert,true));
        }
    
        if (!copy($row['f_name'], $server->config['data-root'].'/'.$sbm_index_insert[':file'])) {
            throw new Exception("Failed to move file {$row['f_name']}");
        }
        $cnt++;
    }    
    $server->pdo->commit();
    echo "{$cnt} records converted\n";
    echo "done\n";
    echo "</pre></body></html>";
}
catch (Exception $e) {
    $server->pdo->rollBack();
    die($e->getMessage());
}

