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

if (!$server->checkPermsArray(['initEmployeeReview','reviewEmployee'])) {
    $server->notAuthorized(true);
}

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
   $review = new Review($server->pdo,$_REQUEST['eid']);
   switch($review->status) {
       case Review::IN_REVIEW: displayOngoingReview($review); break;
       case Review::NOT_IN_REVIEW: displayInitReview($review); break;
   }
}

function displayOngoingReview (Review $review) {
    global $server;
    include('submenu.php');
    $view = $server->getViewer();
    $view->sideDropDownMenu($submenu);
    $view->h1("<small>Ongoing Review for:</small> ".$review->getFullName());
    $view->h3("<small>Began: ".$review->getStartDate());
    $view->h3("<small>Ends: ".$review->getEndDate());
    echo "<span class='bg-info'>The following data represents this timeframe: <mark>".Review::DATA_TIMEFRAME."</mark></span>";

    //Training data
    $view->hr();
    $view->beginBtnCollapse("Show/Hide Training");
    $view->h2("Training");
    $view->responsiveTableStart(['Training','Date','Trainer']);
        foreach($review->getTraining() as $row) {
            echo "<tr><td>{$row['training']}</td><td>{$row['train_date']}</td><td>{$row['trainer']}</td></tr>\n";
        }
    $view->responsiveTableClose();
    $view->endBtnCollapse();

    //Attendace data presentation
    $view->hr();
    $view->beginBtnCollapse('Show/Hide Attendance');
    $view->h2("Review Attendance");
    $view->responsiveTableStart(['Date','Arrived Late','Left Early','Absent','Excused','Reason']);
    foreach($review->getReviewAttendance() as $row) {
        if ($row['absent'] == 'true') $absent = 'Yes';
        else $absent = 'No';
        if ($row['excused'] == 'true') $excused = 'Yes';
        else $excused = 'No';
        echo "<tr><td>{$row['occ_date']}</td><td>{$row['arrive_time']}</td><td>{$row['leave_time']}</td><td>{$absent}</td><td>{$excused}</td><td>{$row['description']}</td></tr>\n";
    }
    $view->responsiveTableClose();
    $view->endBtnCollapse();

    //Supervisor comments
    $view->hr();
    $view->beginBtnCollapse('Show/Hide Management Comments');
    $view->h2("Management Comments");
    $view->responsiveTableStart(['Date','Author','Comments']);
    foreach($review->getReviewManagementComments() as $row) {
        echo "<tr><td>{$row['_date']}</td><td>{$row['author']}</td><td>{$row['comments']}</td></tr>\n";
    }
    $view->responsiveTableClose();
    $view->endBtnCollapse();

    //Review Comments
    
}