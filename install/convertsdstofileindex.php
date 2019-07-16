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
echo "<pre>";
echo "Converting SDS file index to File index...";
$cnt = 0;
try {
    $pntr = $server->pdo->query('SELECT * FROM sds');
    $pntr1 = $server->pdo->prepare('INSERT INTO file_index VALUES (:id,:file,:mime,:reference,:ref_id,:orig,now(),:uid)');
    $server->pdo->beginTransaction();
    foreach($pntr->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $pntr1->execute([
            ':id' => uniqid(),
            ':file' => $row['file'],
            ':mime' => mime_content_type($server->config['data-root'].'/'.$row['file']),
            ':reference' => \SDS_FILE_REF,
            ':ref_id' => $row['id'],
            ':orig' => '?',
            ':uid' => 'conversion_file'
        ]);
        $cnt++;
    }
    $server->pdo->commit();
    echo "{$cnt} records processed successfully!";
}
catch (Exception $e) {
    $server->pdo->rollBack();
    echo $e->getMessage();
}