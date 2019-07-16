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
include('./submenu.php');

$server->userMustHavePermission('adminAll');
$app = new Application($server->pdo);

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
    ($app->deletePermission($_REQUEST['perm']) &&
    $server->redirect('/admin/permissions')) ||
    $server->newEndUserDialog(
        "Unable to delete, something went wrong.",
        DIALOG_FAILURE,
        $server->config['application-root'].'/admin/permissions'
    );
}
elseif (!empty($_REQUEST['newperm'])) {
    ($app->addPermission($_REQUEST['newperm']) && 
    $server->redirect('/admin/permissions')) ||
    $server->newEndUserDialog(
        "Something went wrong with the request.",
        DIALOG_FAILURE,
        $server->config['application-root'].'/admin/permissions'
    );
}

$perms = $app->getPermission();
$view = $server->getViewer("Application Settings");
$view->sideDropDownMenu($submenu);
$form = new FormWidgets($view->PageData['approot'].'/scripts');
$form->newForm('Permission Sets');

echo "<div class='row'>\n";
echo "<div class='col-md-3'></div>\n";
echo "<div class='col-md-6 col-xs-12'>\n";
echo "<div class='table-responive'>\n";
echo "<table class='table'>\n";
echo "<tr><th>Permission</th><th>Delete</th></tr>";
foreach($perms as $row) {
    echo "<tr>\n<td>{$row['name']}</td>\n";
    echo "<td><a href='?action=delete&perm={$row['id']}' class='btn btn-xs btn-danger' role='button'>";
    echo "<span class='glyphicon glyphicon-trash'></span></a></td>\n</tr>\n";
}
echo "</table>\n</div>\n</div>\n";
echo "<div class='col-md-3'></div>\n</div>\n";

$form->inputCapture('newperm','New Permission',null,true);
$form->submitForm();
$form->endForm();
$view->footer();