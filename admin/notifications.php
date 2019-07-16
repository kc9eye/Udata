<?php
/* This file is part of UData.
 * Copyright (C) 2019 Paul W. Lane <kc9eye@outlook.com>
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

if (!empty($_REQUEST['action'])) {
    switch($_REQUEST['action']) {
        case 'delete':
            $notify = new Notification($server->pdo,$server->mailer);
            $server->processingDialog(
                [$notify,'deleteNotification'],
                [$_REQUEST['id']],
                $server->config['application-root'].'/admin/main'
            );
        break;
        case 'add':
            $notify = new Notification($server->pdo,$server->mailer);
            $server->processingDialog(
                [$notify,'addNewNotification'],
                [$_REQUEST],
                $server->config['application-root'].'/admin/main'
            );
        break;
        default:
            displayAddFrom();
        break;
    }
}
else 
    displayAddForm();

function displayAddForm () {
    global $server;
    $view = $server->getViewer("Admin: Notifications");
    $form = new FormWidgets($view->PageData['approot']);

    $view->h1("Add New Notification",true);
    $form->newForm();
    $form->hiddenInput('action','add');
    $form->hiddenInput('uid',$server->currentUserID);
    $form->hiddenInput('type',Notification::EMAIL_TYPE);
    $form->inputCapture('description','Description',null,true,"What the notification is for?");
    $form->submitForm('Add',false,$server->config['application-root'].'/admin/main');
    $form->endForm();

    $view->footer();
}