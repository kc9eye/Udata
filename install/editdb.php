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
require_once(dirname(__DIR__).'/etc/config.php');
$dbh = new PDO($config['dbpdo'],$config['dbuser'],$config['dbpass']);
echo "<pre>";
try{
    echo "\n";
    echo "Attempting to convert file names... ";
    
    $pntr = $dbh->query('select id, file from sds');
    $dbh->beginTransaction();
    while (($row = $pntr->fetch(PDO::FETCH_ASSOC))) {
        $dbh->query("UPDATE sds SET file = '".basename($row['file'])."' WHERE id = '{$row['id']}'");
    }
    $dbh->commit();
    echo "DONE\n";
}
catch (Exception $e) {
    $dbh->rollback();
    echo "FAILED";
    var_dump($e);
}
finally {
    echo "</pre>";
}