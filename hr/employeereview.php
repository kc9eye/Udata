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
    $view->bgInfoParagraph(
        "The purpose of conducting the Performace Appraisal is to:
        <ol>
            <li>Develop better <i>communication</i> between the employee and the supervisor</li>
            <li>Improve the <i>quality</i> of work and safety</li>
            <li>Increase productivity and promote employee development</li>
        </ol>"
    );

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
    $view->hr();
    $view->h2("Appraisals");
    $view->bgInfoParagraph(
        "When creating your appraisal, the following 6 points should be considered and touched upon:
        <ol>
            <li><strong>Safety Orientation</strong>: <i>Considers the safety of self as well as co-workers</i></li>
            <li><strong>Attendance</strong>: <i>Has acceptable attendance record; arrives on time and completes scheduled work hours</i></li>
            <li><strong>Work Ethic</strong>: <i>Follows directions promptly and accurately; is flexible; demonstrates initiative; works with minimal supervision</i></li>
            <li><strong>Judgement/Descision Making</strong>: <i>Has good communication skills.</i></li>
            <li><strong>Attitude</strong>: <i>Presents a positive attitude; demonstrates honesty and integrity; polite"
    )
    $view->beginBtnCollapse("Show/Hide Other's Appraisals");

}