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


if (!empty($_REQUEST['emp_search'])) {
    $model = new Employees($server->pdo);
    $formater = new SearchStringFormater();
    $results = $model->searchEmployees($formater->formatSearchString($_REQUEST['emp_search']));
    resultsDisplay($results);
}
else searchDisplay();

function searchDisplay () {
    global $server;
    include('submenu.php');
    $server->userMustHavePermission('viewProfiles');
    $view = $server->getViewer('HR: Profiles');
    $view->sideDropDownMenu($submenu);
    $form = new InlineFormWidgets($view->PageData['wwwroot'].'/scripts');
    if ($server->checkPermission('addNewProfile'))
        $view->h1('Search Employee Profiles '.$view->linkButton('/hr/addnew',"<span class='glyphicon glyphicon-plus'></span> Add New Employee",'info',true));
    else
        $view->h1('Search Employee Profiles');
    $form->fullPageSearchBar('emp_search','Employee Search',null,false,"Search for people to add injuries,training,comments");
    $view->footer();
}

function resultsDisplay ($results) {
    global $server;
    $view = $server->getViewer("HR: Employees");
    include('submenu.php');
    $view->sideDropDownMenu($submenu);
    $form = new InlineFormWidgets($view->PageData['wwwroot'].'/scripts');
    if ($server->checkPermission('addNewProfile'))
        $view->h1('Search Employee Profiles '.$view->linkButton('/hr/addnew',"<span class='glyphicon glyphicon-plus'></span> Add New Employee",'info',true));
    else
        $view->h1('Search Employee Profiles');
    $form->fullPageSearchBar('emp_search','Employee Search',null,false,"Search for people to add injuries,training,comments");
    if (empty($results)) {
        $view->bold("Nothing Found matching: {$_REQUEST['emp_search']}");
    }
    else {
        $view->responsiveTableStart(['Name','Start Date','End Date']);
        foreach($results as $row) {
            echo "<tr><td><a href='{$server->config['application-root']}/hr/viewemployee?id={$row['id']}'>{$row['name']}<a></td>";
            echo "<td>{$row['start_date']}</td><td>{$row['end_date']}</td></tr>\n";
        }
        $view->responsiveTableClose();
    }
    $view->footer();
}