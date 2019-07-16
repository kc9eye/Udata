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

switch($_REQUEST['id']) {
    case 'reset':
        if (file_exists($server->config['error-log-file-path'])) {
            if (unlink($server->config['error-log-file-path']))
                $server->newEndUserDialog(
                    "Error log reset successful.",
                    DIALOG_SUCCESS,
                    $server->config['application-root'].'/admin/main'
                );
            else 
                $server->newEndUserDialog(
                    "Unable to reset log file.",
                    DIALOG_FAILURE,
                    $server->config['application-root'].'/admin/main'
                );
        }
        else 
            $server->newEndUserDialog(
                "Error log file:{$server->config['error-log-file-path']} not found.",
                DIALOG_FAILURE,
                $server->config['application-root'].'/admin/main'
            );
    break;
    default:displayError();break;
}

function displayError () {
    global $server;
    include('submenu.php');
    $log = simplexml_load_file($server->config['error-log-file-path']);
    $view = $server->getViewer("Error: {$_REQUEST['id']}");
    $view->sideDropDownMenu($submenu);
    foreach($log->error as $err) {
        if ($err->id == $_REQUEST['id']) $the_error = $err;
    }
    $view->responsiveTableStart(null,true);
    echo "<tr><th>ID:</th><td>{$the_error->id}</td></tr>\n";
    echo "<tr><th>Date:</th><td>{$the_error->date}</td></tr>\n";
    echo "<tr><th>Error Code:</th><td>{$the_error->number}</td></tr>\n";
    echo "<tr><th>File:</th><td>{$the_error->file}</td></tr>\n";
    echo "<tr><th>Line:</th><td>{$the_error->line}</td></tr>\n";
    echo "<tr><th>Message:</th><td>{$the_error->message}</td></tr>\n";
    $view->responsiveTableClose(true);
    $view->beginBtnCollapse("Show/Hide Trace");
    $view->wrapInPre(html_entity_decode($the_error->trace));
    $view->endBtnCollapse();
    $view->footer();
}