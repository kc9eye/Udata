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
$server->userMustHavePermission('adminAll');
include(UpdateDatabase::DB_VERSION_FILE);

$app = new Application($server->pdo);
$notify = new Notification($server->pdo,$server->mailer);

$view = $server->getViewer("Application Settings");
$form = new InlineFormWidgets($view->PageData['wwwroot'].'/scripts');

$form->inlineButtonGroup([
    'Documentation'=>"window.open(\"{$view->PageData['approot']}/docs/api/index.html\",\"_blank\")",
    'Framework'=>"window.open(\"{$view->PageData['approot']}/docs/database_structure/UData_Database_Structure.html\",\"_blank\")"
]);

$view->responsiveTableStart(['Package Name','Current Version']);
echo "<tr><td>UData Framework</td><td>".\APP_VERSION."</td></tr>";
echo "<tr><td>UDatabase Schema</td><td>{$current_version}</td></tr>";
echo "<tr><td>Bootstrap UI</td><td>".\BOOTSTRAP_VERSION."</td></tr>";
echo "<tr><td>PHPMailer</td><td>".file_get_contents(\INCLUDE_ROOT.'/third-party/PHPMailer/VERSION')."</td></tr>";
$view->responsiveTableClose();

//User Adminstration
$view->hr();
$view->h2("User Administration");
$users = empty($_REQUEST['usersearch']) ? [] : $app->searchUsers($_REQUEST['usersearch']);
$form->fullPageSearchBar('usersearch','Search Users');
if (empty($users)) {
    $view->bold('No users found to match.');
}
else {
    $view->h2("Search Results");
    echo "<div class='table-responsive'><table class='table'>";
    foreach ($users as $row) {
        echo "<tr><td><a href='{$view->PageData['approot']}/admin/users?uid={$row['id']}'>{$row['firstname']} {$row['lastname']}</td><td>{$row['username']}</td></tr>";
    }
    echo "</table></div>";
}


//Security Model
$view->hr();
$view->h2("Security Model");
$view->beginBtnCollapse();
$view->br();
$form->inlineButtonGroup([
    'Add/Edit Roles'=>"window.open(\"{$view->PageData['approot']}/admin/roles\",\"_self\")",
    'Add/Edit Permissions'=>"window.open(\"{$view->PageData['approot']}/admin/permissions\",\"_self\")"
]);
$view->hr();
foreach($app->getRole() as $role) {
    $perms = $app->getPermsFromRole($role['id']);
    $view->h3($role['name']."&#160;".$view->editBtnSm("/admin/editrole?rid={$role['id']}",true));
    if (!empty($perms)) {
        echo "<ol class='list-inline'>";
        foreach($perms as $perm) {
            echo "<li class='list-inline-item'>{$perm['name']}</li>";
        }
        echo "</ol>";
    }
    $view->hr();
}
$view->endBtnCollapse();

//Application Notifications
$view->hr();
$view->h2("Notifications");
$view->beginBtnCollapse();
$view->br();
$form->inlineButtonGroup([
    'Add Notifications'=>"window.open(\"{$view->PageData['approot']}/admin/notifications\",\"_self\");"
]);
$view->responsiveTableStart();
foreach($notify->getAllNotifications() as $row) {
    echo "<tr><td>{$row['description']}</td><td>";
    $view->trashBtnSm('/admin/notifications?action=delete&id='.$row['id']);
    echo "</td></tr>";
}
$view->responsiveTableClose();
$view->endBtnCollapse();

//Application Settings
$view->hr();
$view->h2("Current Application Settings");
$view->beginBtnCollapse();
$view->responsiveTableStart(['Variable Name','Current Value']);
foreach($server->config as $index => $value) {
    if (is_string($value)) {
        if ($index == 'dbpass') echo "<tr><td>{$index}</td><td>REDACTED</td></tr>";
        else echo "<tr><td>{$index}</td><td>{$value}</td></tr>";
    }
    elseif (is_array($value)) {
        echo "<tr><td>{$index}</td><td>";
        echo "<ul>";
        foreach($value as $i=>$v) {
            if (is_string($v))
                echo "<li>{$i} = {$v}</li>";
            elseif (is_array($v)) {
                echo "<li>{$i} = <ul>";
                foreach($v as $vi) {
                    echo "<li>{$vi}</li>";
                }
                echo "</ul>";
            }
        }
        echo "</ul>";
        echo "</td></tr>";
    }
}
$view->responsiveTableClose();
$view->endBtnCollapse();

//Error log
$view->hr();
$view->h2("Error Log");
$view->beginBtnCollapse();
$view->br();
if (file_exists($server->config['error-log-file-path'])) {
    $view->linkButton('/admin/errlog?id=reset','Reset Log File','danger');
    $view->br();
    $log = simplexml_load_file($server->config['error-log-file-path']);
    $entries = $log->xpath('error');
    $entries = array_reverse($entries);
    echo "<div class='list-group' style='height:300px;overflow-y:scroll;width:100%'>";
    foreach($entries as $err) {
        echo "<a href='{$view->PageData['approot']}/admin/errlog?id={$err->id}' class='list-group-item list-group-item-action'>";
        $view->bold("Error:&nbsp;");
        echo "{$err->id} <span class='badge badge-info float-right m-2'>{$err->date}</span></a>";
    }
    echo "</div>";
}

$view->endBtnCollapse();

$view->addScrollTopBtn();
$view->footer();