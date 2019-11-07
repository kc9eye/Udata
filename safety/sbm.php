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

$edit = $server->checkPermission('editSBM');

if (!empty($_REQUEST['action'])) {
    switch($_REQUEST['action']) {
        default: main(); break;
    }
}
else {
    main();
}

function main () {
    global $server;
    include('submenu.php');

    $sbm = new SafetyBoardMinutes($server->pdo,$server->config['data-root']);
    $view = $server->getViewer('Safety Board Minutes');
    $view->sideDropDownMenu($submenu);
    $view->h1('Safety Meeting Minutes');
    $th = $edit ? ['Meeting Date','Delete'] : ['Meeting Date'];
    $view->responsiveTableStart($th);
    foreach 
    $view->footer();
}