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

$server->userMustHavePermission('addProduct');

if (!empty($_REQUEST['action'])) {
    $products = new Products($server->pdo);
    switch($_REQUEST['action']) {
        case 'add':
            unset($_REQUEST['action']);
            $server->processingDialog([$products,'addCheckPoint'],[$_REQUEST],$server->config['application-root']."/cells/cellcheckpoints?id={$_REQUEST['cellid']}");
        break;
        case 'remove':
            unset($_REQUEST['action']);
            $server->processingDialog([$products,'removeCheckPoint'],[$_REQUEST['prokey'],$_REQUEST['qcpid']],$server->config['application-root']."/cells/cellcheckpoints?id={$_REQUEST['cellid']}");
        break;
    }
}

$wc = new WorkCell($server->pdo,$_REQUEST['id']);

$view = $server->getViewer("Work Cells: QCP");
$form = new FormWidgets($view->PageData['wwwroot'].'/scripts');

$form->newForm("<small>CheckPoints For:</small> {$wc->Name}<br /><small>Assoc. Product:</small> {$wc->Product}");

echo "<div class='row'><div class='col-md-3'></div><div class='col-md-6 col-xs-12'><div class='table-responive'><table class='table'>\n";
echo "<tr><th>Description</th><th>Remove</th></tr>\n";
foreach($wc->QCP as $row) {
    echo "<tr><td>{$row['description']}</td><td><a href='?action=remove&cellid={$wc->ID}&qcpid={$row['id']}&prokey={$wc->ProductKey}' class='btn btn-sm btn-danger' role='button'>
    <span class='glyphicon glyphicon-trash'></span></a></td></tr>\n";
}
echo "</table></div></div><div class='col-md-3'></div></div>\n";
$form->hiddenInput('action','add');
$form->hiddenInput('cellid',$_REQUEST['id']);
$form->hiddenInput('prokey',$wc->ProductKey);
$form->hiddenInput('uid',$server->currentUserID);
$form->inputCapture('description','CheckPoint',null,true);
$form->submitForm('Add',false,$server->config['application-root']."/cells/main?action=view&id={$_REQUEST['id']}");

$view->footer();
