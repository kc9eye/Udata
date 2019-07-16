<?php
/* This file is part of UData
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
if (!empty($_REQUEST)) {
    $app->addRole($_REQUEST['role']) ||
    $server->newEndUserDialog(
        "Smoething went wrong with the request",
        DIALOG_FAILURE,
        $server->config['application-root'].'/admin/roles'
    );
}

$content = $app->getRole();

$view = $server->getViewer('Application Settings');
$view->sideDropDownMenu($submenu);
$form = new FormWidgets($view->PageData['wwwroot'].'/scripts');

$form->newForm();
echo "<div class='row'>\n";
echo "<div class='col-md-3'></div>\n";
echo "<div class='col-xs-12 col-md-6'>\n";
$view->h3("User Role");
echo "<div class='table-responsive'>\n";
echo "<table class='table'>\n";
echo "<tr><th>Role Name</th><th>Edit</th></tr>\n";
foreach ($content as $row) {
    echo "<tr><td>{$row['name']}</td><td><a href='{$view->PageData['approot']}/admin/editrole?rid={$row['id']}' class='btn btn-xs btn-warning' role='button'>";
    echo "<span class='glyphicon glyphicon-pencil'></span></a></td></tr>\n";
}
echo "</table>\n</div>\n";
echo "</div>\n";
echo "<div class='col-md-3'></div>\n";
echo "</div>";
$form->inputCapture('role', 'Add Role', null, true);
$form->submitForm();
$form->endForm();
$view->hr();

$view->footer();

