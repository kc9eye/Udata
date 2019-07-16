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

$server->userMustHavePermission('editSupervisorComments');

if (!empty($_REQUEST['action'])) {
    switch($_REQUEST['action']) {
        case 'add':
            $server->processingDialog(
                'addNewComment',
                [],
                $server->config['application-root'].'/hr/viewemployee?id='.$_REQUEST['id']
            );
        break;
        case 'view':
            viewCommentDisplay();
        break;
        default:
            commentFormDisplay();
        break;
    }
}
else 
    commentFormDisplay();

function commentFormDisplay () {
    global $server;
    include('submenu.php');
    $emp = new Employee($server->pdo,$_REQUEST['id']);
    $view = $server->getViewer('HR: Comments');
    $view->sideDropDownMenu($submenu);
    $form = new FormWidgets($view->PageData['wwwroot'].'/scripts');
    $view->h1("<small>Add Comment to:</small> {$emp->Profile['first']} {$emp->Profile['middle']} {$emp->Profile['last']} {$emp->Profile['other']}",true);
    $form->newForm();
    $form->hiddenInput('action','add');
    $form->hiddenInput('eid',$_REQUEST['id']);
    $form->hiddenInput('uid',$server->currentUserID);
    $view->h2('Comments:',true);
    $form->textArea('comments',null,'',true,'Enter comments for the individual',true);
    $form->submitForm('Submit',false,$server->config['application-root'].'/hr/viewemployee?id='.$_REQUEST['id']);
    $form->endForm();
    $view->footer();
}

function viewCommentDisplay () {
    global $server;
    include('submenu.php');

    $handler = new SupervisorComments($server->pdo);
    $comment = $handler->getComment($_REQUEST['id']);

    $view = $server->getViewer('HR: Comments');
    $view->sideDropDownMenu($submenu);
    $view->linkButton('/hr/viewemployee?id='.$comment['eid'],"<span class='glyphicon glyphicon-arrow-left'></span> Back");
    $view->responsiveTableStart();
    echo "<tr><th>Employee Name:</th><td>{$comment['name']}</td></tr>\n";
    echo "<tr><th>Comment ID:</th><td>{$comment['id']}</td></tr>\n";
    echo "<tr><th>Comment Author:</th><td>{$comment['author']}</td></tr>\n";
    echo "<tr><th>Comment Date/Time:</th><td>{$comment['date']}</td></tr>\n";
    echo "<tr><th>Comment:</th><td>{$comment['comments']}</td></tr>\n";
    $view->responsiveTableClose();
    $view->footer();
}

function addNewComment () {
    global $server;
    $handler = new SupervisorComments($server->pdo);
    $notify = new Notification($server->pdo,$server->mailer);

    if ($handler->addNewSupervisorFeedback($_REQUEST)) {
        $body = file_get_contents(INCLUDE_ROOT.'/wwwroot/templates/email/supervisorfeedback.html');
        $body .= "<a href='{$server->config['application-root']}/hr/feedback?action=view&id={$handler->newCommentID}'>View Supervisor Feedback</a>";
        $notify->notify('New Supervisor Comment','New Supervisor Comment',$body);
        return true;
    }
    else {
        return false;
    }
}