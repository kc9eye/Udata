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

$server->userMusthavePermission('adminAll');

if (!empty($_REQUEST['action'])) {
    switch($_REQUEST['action']) {
        case 'addrole':
            $app = new Application($server->pdo);
            $server->processingDialog(
                [$app,'addRoleToUser'],
                [$_REQUEST['rid'],$_REQUEST['uid']],
                $server->config['application-root'].'/admin/users?uid='.$_REQUEST['uid']
            );
        break;
        case 'removerole':
            $app = new Application($server->pdo);
            $server->processingDialog(
                [$app,'removeRoleFromUser'],
                [$_REQUEST['uid'],$_REQUEST['rid']],
                $server->config['application-root'].'/admin/users?uid='.$_REQUEST['uid']
            );
        break;
        case 'delete':
            $app = new Application($server->pdo);
            $server->processingDialog(
                [$app,'deleteUser'],
                [$_REQUEST['uid'],$_REQUEST['pid']],
                $server->config['application-root'].'/admin/main'
            );
        break;
        case 'addnotification':
            $notify = new Notification($server->pdo,$server->mailer);
            $server->processingDialog(
                [$notify,'addNotificationToUser'],
                [$_REQUEST['nid'],$_REQUEST['uid']],
                $server->config['application-root'].'/admin/users?uid='.$_REQUEST['uid']
            );
        break;
        case 'removenotification':
            $notify = new Notification($server->pdo,$server->mailer);
            $server->processingDialog(
                [$notify, 'removeNotificationFromUser'],
                [$_REQUEST['nid'], $_REQUEST['uid']],
                $server->config['application-root'].'/admin/users?uid='.$_REQUEST['uid']
            );
        break;
        default:
            displayUser();
        break;
    }
}
else 
    displayUser();

function displayUser () {
    global $server;
    include('submenu.php');
    $app = new Application($server->pdo);
    $notify = new Notification($server->pdo,$server->mailer);
    $user = $app->getUserData($_REQUEST['uid']);
    $roles = $app->getUserRoles($_REQUEST['uid']);

    #this is needed for the selectBox array form
    $rawunsed = $app->unusedRoleSet($_REQUEST['uid']);
    $unused = [];
    foreach($rawunsed as $array) {
        array_push($unused, [$array['id'],$array['name']]);
    }
    
    $view = $server->getViewer("Application Settings");
    $view->sideDropDownMenu($submenu);
    $form = new InlineFormWidgets($view->PageData['wwwroot'].'/scripts');
    $view->h3(
        "User Info ".
        $view->linkButton('/admin/users?action=delete&uid='.$_REQUEST['uid'].'&pid='.$user['pid'],'Delete','danger',true),
        true
    );
    $view->responsiveTableStart(null,true);
    echo "<tr><th>UID:</th><td>{$user['id']}</td></tr>\n";
    echo "<tr><th>Username:</th><td>{$user['username']}</td></tr>\n";
    echo "<tr><th>First Name:</th><td>{$user['firstname']}</td></tr>\n";
    echo "<tr><th>Last Name:</th><td>{$user['lastname']}</td></tr>\n";
    echo "<tr><th>Alt. Email:</th><td>{$user['alt_email']}</td></tr>\n";
    $view->responsiveTableClose(true);

    $view->h3("User Roles",true);
    $view->responsiveTableStart(null,true);
    foreach($roles as $row) {
        echo "<tr><td>{$row['name']}</td><td>";
        $view->trashBtnSm('/admin/users?action=removerole&rid='.$row['id'].'&uid='.$_REQUEST['uid']);
        echo "</td></tr>\n";
    }
    echo "<tr><td colspan='2'>";
    $form->newInlineForm();
    $form->hiddenInput('action','addrole');
    $form->hiddenInput('uid',$_REQUEST['uid']);
    $form->inlineSelectBox('rid','Add Role',$unused,true);
    $form->inlineSubmit();
    $form->endInlineForm();
    echo "</td></tr>\n";
    $view->responsiveTableClose(true);

    $unused_notifications = array();
    foreach ($notify->getUnusedNotifications($_REQUEST['uid']) as $row) {
        array_push($unused_notifications, [$row['id'],$row['description']]);
    }
    $view->h3("User Notifications",true);
    $view->responsiveTableStart(null,true);
    foreach($notify->getUserNotifications($_REQUEST['uid']) as $row) {
        echo "<tr><td>{$row['description']}</td><td>";
        $view->trashBtnSm('/admin/users?action=removenotification&uid='.$_REQUEST['uid'].'&nid='.$row['id']);
        echo "</td></tr>\n";
    }
    echo "<tr><td colspan='2'>";
    $form->newInlineForm();
    $form->hiddenInput('action','addnotification');
    $form->hiddenInput('uid',$_REQUEST['uid']);
    $form->inlineSelectBox('nid',"Add Notification",$unused_notifications,true);
    $form->inlineSubmit();
    $form->endInlineForm();
    echo "</td></tr>\n";
    $view->responsiveTableClose(true);
    $view->footer();
}