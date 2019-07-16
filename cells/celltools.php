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

//Access permissions check
$server->userMustHavePermission('editWorkCell');

//Controller section, take action and/or decide on view to present
if (!empty($_REQUEST['search_tools'])) {
    $tools = new Maintenance($server->pdo);
    $search_results = $tools->searchTools($_REQUEST['search_tools']);
    editView();
}
elseif (!empty($_REQUEST['action'])) {
    switch($_REQUEST['action']) {
        case 'add':
            addView();
        break;
        case 'commit':
            $cells = new WorkCells($server->pdo);
            $server->processingDialog([$cells,'addToolingToCell'],[$_REQUEST],$server->config['application-root'].'/cells/celltools?id='.$_REQUEST['cellid']);
        break;
        case 'remove':
            $cells = new WorkCells($server->pdo);
            $server->processingDialog([$cells,'removeToolingFromCell'],[$_REQUEST['toolid']],$server->config['application-root'].'/cells/celltools?id='.$_REQUEST['cellid']);
        default:
            editView();
        break;
    }
}
else {
    editView();
}

//Available views
function editView () {
    global $server,$search_results;
    $cell = new WorkCell($server->pdo,$_REQUEST['id']);
    $view = $server->getViewer("Workcell: Tooling");
    $form = new FormWidgets($view->PageData['wwwroot'].'/scripts');
    $view->h2("<small>Add Tooling to:</small> {$cell->Name} 
        <a href='{$view->PageData['approot']}/cells/main?action=view&id={$_REQUEST['id']}' class='btn btn-info' role='button'>
            <span class='glyphicon glyphicon-arrow-left'></span> Back
        </a>"
    );
    $view->hr();
    $view->responsiveTableStart(['Description','Category','Remove'],true);
    foreach($cell->Tools as $row) {
        echo "<tr><td>{$row['description']}</td><td>{$row['category']}</td>";
        echo "<td><a href='?action=remove&toolid={$row['id']}&cellid={$_REQUEST['id']}' class='btn btn-danger' role='button'><span class='glyphicon glyphicon-trash'></span></a>";
    }
    $view->responsiveTableClose(true);
    $view->hr();
    $form->searchBox('search_tools','Search for Tools to Add',null,false,"To narrow search results include '&' or 'AND' between search terms.");
    if (!empty($search_results)) {
        if (is_array($search_results)) {
            $view->responsiveTableStart(['Description','Category'],true);
            foreach($search_results as $row) {
                echo "<tr><td><a href='?action=add&id={$row['id']}&cellid={$_REQUEST['id']}'>{$row['description']}</td><td>{$row['category']}</td></tr>\n";
            }
            $view->responsiveTableClose(true);
        }
        else {
            $view->bold($_REQUEST['search_tools']);
            echo " not found.";
        }
    }
    $view->footer();
}

function addView () {
    global $server;
    $cell = new WorkCell($server->pdo,$_REQUEST['cellid']);
    $tool = new Tool($server->pdo,$_REQUEST['id']);
    $view = $server->getViewer("WorkCell: Add Tool");
    $form = new FormWidgets($view->PageData['wwwroot'].'/scripts');
    $view->h2("<small>Add Tool to:</small>{$cell->Name}",true);
    $form->newForm();
    $form->hiddenInput('cellid',$_REQUEST['cellid']);
    $form->hiddenInput('toolid',$_REQUEST['id']);
    $form->hiddenInput('uid',$server->currentUserID);
    $form->hiddenInput('action','commit');
    $form->inputCapture('qty','Quantity','1',true,'How many of these tools are required?');
    $form->labelContent('Tool',$tool->Description);
    $form->submitForm('Add',false,'?id='.$_REQUEST['cellid']);
    $form->endForm();
    $view->footer();
}