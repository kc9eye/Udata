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

if (empty($_REQUEST['rid'])) {
    $server->newEndUserDialog(
        "You must first pick a role to edit.".
        DIALOG_FAILURE,
        $server->config['application-root'].'/admin/main'
    );
}
elseif (!empty($_REQUEST['action'])) {
    switch($_REQUEST['action']) {
        case 'removerole':
            ($app->removePermFromRole($_REQUEST['permid'],$_REQUEST['rid']) &&
            $server->redirect('/admin/editrole?rid='.$_REQUEST['rid'])) ||
            $server->newEndUserDialog(
                "Something went wrong with the request.",
                DIALOG_FAILURE,
                $server->config['application-root'].'/admin/editrole'
            );
        break;
        case 'delete':
            (
                $app->deleteRole($_REQUEST['rid']) &&
                $server->newEndUserDialog(
                    "Succesfully deleted role.",
                    DIALOG_SUCCESS,
                    $server->config['application-root'].'/admin/roles'
                ) && 
                $server->redirect('/admin/editrole?rid='.$_REQUEST['rid'])

             ) || $server->newEndUserDialog(
                 "Something went wrong with the request",
                 DIALOG_FAILURE,
                 $server->config['application-root'].'/admin/roles'
             );
        break;
        default:
            $server->newEndUserDialog(
                "Something went horribly wrong.",
                DIALOG_FAILURE,
                $server->config['application-root'].'/admin/editrole'
            );
        break;
    }
}
elseif (!empty($_REQUEST['permid'])) {
        ($app->addPermToRole($_REQUEST['permid'],$_REQUEST['rid']) &&
        $server->redirect('/admin/editrole?rid='.$_REQUEST['rid'])) ||
        $server->newEndUserDialog(
            "Something went wrong with the request",
            DIALOG_FAILURE,
            $server->config['application-root'].'/admin/editrole?rid='.$_REQUEST['rid']
        );
}

$role = $app->getRole($_REQUEST['rid']);
$perms = $app->getPermsFromRole($_REQUEST['rid']);

#gets the unused array in the form needed for selecBox
$unused = [];
foreach($app->unusedPermissionSet($_REQUEST['rid']) as $row) {
    array_push($unused, [ $row['id'],$row['name'] ]);
}

$view = $server->getViewer("Application Settings");
$view->sideDropDownMenu($submenu);
$form = new FormWidgets($view->PageData['wwwroot'].'/scripts');

$form->newForm(
    "<small>Edit:</small>{$role[0]['name']}
     <a href='?action=delete&rid={$_REQUEST['rid']}' class='btn btn-large btn-danger' role='button'>Delete</a>"
);
echo "<div class='row'><div class='col-md-3'></div>\n";
echo "<div class='col-xs-12 col-md-6'>\n";
$view->h3("Role Permission Set");
echo "<div class='table-responsive'>\n";
echo "<table class='table'><tr><th>Permission</th><th>Remove From Role</th></tr>\n";
foreach($perms as $row) {
    echo "<tr><td>{$row['name']}</td>\n";
    echo "<td><a href='?action=removerole&rid={$_REQUEST['rid']}&permid={$row['id']}' class='btn btn-xs btn-danger' role='button'>\n";
    echo "<span class='glyphicon glyphicon-minus'></span></a></td></tr>\n";
}
echo "</table></div></div><div class='col-md-3'></div></div>\n";
$form->selectBox('permid','Add To Set',$unused);
$form->submitForm();
$form->endForm();
$view->footer();