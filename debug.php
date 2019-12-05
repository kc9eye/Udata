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
require_once('lib/init.php');

$server->userMustHavePermission('adminAll');

if (!empty($_REQUEST['action'])) {
    switch($_REQUEST['action']) {
        case 'submit':
            displayDebug();
        break;
        default: main(); break;
    }
}
else {
    main();
}

function main () {
    global $server;
    $view = $server->getViewer('DEBUG');
    $view->h1('Debug Content');
    $form = new FormWidgets($view->PageData['wwwroot']);
    $form->newMultipartForm();
    $form->hiddenInput('action','submit');
    $form->hiddenInput('uid',$server->currentUserID);
    $form->inputCapture('prokey','Prokey',null,true);
    $form->fileUpload(FileIndexer::UPLOAD_NAME,'File',null,true);
    $form->submitForm();
    $form->endForm();
    $view->footer();
}

function displayDebug () {
    global $server;
    $_REQUEST['file'] = new FileUpload(FileIndexer::UPLOAD_NAME);
    $bom = new BillOfMaterials($server->pdo);
    $server->getDebugViewer($bom->rebaseExistingBOM($_REQUEST));
}