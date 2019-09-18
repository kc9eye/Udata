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
        case 'initreview':
            $server->userMustHavePermission('initEmployeeReview');
            $handler = new Employees($server->pdo);
            $server->processingDialog(
                [$handler,'initiateReview'],
                [$server,$_REQUEST['eid']],
                $server->config['application-root'].'/hr/employeereview?eid='.$_REQUEST['eid']
            );
        break;
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
    $view = $server->getViewer('Review: '.$review->getFullName());
    $form = new InlineFormWidgets($view->PageData['wwwroot'].'/scripts');
    $view->sideDropDownMenu($submenu);
    $view->h1("<small>Ongoing Review for:</small> ".$review->getFullName());
    $view->h3("<small>Began:</small> ".$review->getStartDate());
    $view->h3("<small>Ends:</small> ".$review->getEndDate());
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
        echo "<tr><td>{$row['date']}</td><td>{$row['author']}</td><td>{$row['comments']}</td></tr>\n";
    }
    $view->responsiveTableClose();
    $view->endBtnCollapse();

    //Review Comments
    $view->hr();
    $view->h2("Appraisal");
    $view->bgInfoParagraph(
        "When creating your appraisal, the following 6 points should be considered and touched upon:
        <ol>
            <li><strong>Safety Orientation</strong>: <i>Considers the safety of self as well as co-workers</i></li>
            <li><strong>Attendance</strong>: <i>Has acceptable attendance record; arrives on time and completes scheduled work hours</i></li>
            <li><strong>Work Ethic</strong>: <i>Follows directions promptly and accurately; is flexible; demonstrates initiative; works with minimal supervision</i></li>
            <li><strong>Judgement/Descision Making</strong>: <i>Has good communication skills.</i></li>
            <li><strong>Attitude</strong>: <i>Presents a positive attitude; demonstrates honesty and integrity; polite and approachable; works well with others; is team orientated.</i></li>
            <li><strong>Housekeeping</strong>: <i>5S orientated; makes effort to maintain a safe and clean work area, on a consistent basis.</i></li>
        </ol>"
    );
    //Others appraisals
    $view->beginBtnCollapse("Show/Hide Other's Appraisals");
    $otherappraisals = $review->getOthersAppraisals($server->currentUserID);   
    if ($otherappraisals === false) {
        $view->bold("No other appraisals found.");
    }
    else {
        echo "<div class='panel-group'>\n"; 
        foreach($otherappraisals as $row) {
            echo "  <div class='panel panel-primary'>\n";
            echo "      <div class='panel-heading'>Reviewers Appraisal</div>\n";
            echo "      <div class='panel-body'>{$row['comments']}</div>\n";
            echo "  </div>\n";
        }
        echo "</div>";
    }
    $view->endBtnCollapse();
    //Your appraisal
    $myappraisal = $review->getUserAppraisal($server->currentUserID);
    if ($myappraisal === false) $myappraisal = '';

    $form->newForm('My Appraisal');
    $form->hiddenInput('action','update_appraisal');
    $form->hiddenInput('uid',$server->currentUserID);
    $form->hiddenInput('revid',$review->getReviewID());
    $form->inlineTextArea('appraisal', null, $myappraisal, true, null, true);
    $form->submitForm('Submit',true, $view->PageData['approot'].'/hr/viewemployee?id='.$review->eid);
    $form->endForm();

    $view->footer();
}

function displayInitReview ($review) {
    global $server;
    include('submenu.php');
    $view = $server->getViewer('Review: '.$review->getFullName());
    $view->sideDropDownMenu($submenu);
    $view->h2($review->getFullName()." <small class='bg-danger'>Is currently not in review</small>");
    if ($server->checkPermission('initEmployeeReview')) {
        $view->h3(
            'You can <i class=\'bg-primary\'>initiate</i> the review process here: '.$view->linkButton('/hr/employeereview?eid='.$review->eid.'&action=initreview','Begin Review Process','danger',true)
        );
        $view->hr();
        $view->h3(
            'You can <i class=\'bg-primary\'>view</i> the last review here: '.$view->linkButton('/hr/employeereview?eid='.$review->eid.'&action=viewlast', 'View Last Review','info',true)
        );
    }

    $view->footer();
}