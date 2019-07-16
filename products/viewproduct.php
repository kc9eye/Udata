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
include('./submenu.php');

//Access permissions and request handling
$server->userMustHavePermission('viewProduct');

if (empty($_REQUEST['prokey'])) {
    $server->redirect('/products/main');
}
elseif (!empty($_REQUEST['begin']) && !empty($_REQUEST['end'])) {
    $beginDate = $_REQUEST['begin'];
    $endDate = $_REQUEST['end'];
    $product = new Product($server->pdo,$_REQUEST['prokey'],$beginDate,$endDate);
}
else {
    $product = new Product($server->pdo,$_REQUEST['prokey']);
    $beginDate = date('Y/m/d',strtotime($product->pCreateDate));
    $endDate = date('Y/m/d',time());
}

//View header options for adding the Boostrap DatePicker
$pageOptions = [
    'headinserts'=> [
        '<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>',
        '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>'
    ]
];

//Start the main product view
$view = $server->getViewer("Products:Viewer", $pageOptions);
$form = new InlineFormWidgets($view->PageData['wwwroot'].'/scripts');

//If user has correct permissions show edit button.
if ($server->checkPermission('addProduct')) {
    $view->h1(
        "<small>Viewing:</small> {$product->pDescription}
         <a href='{$server->config['application-root']}/products/editproduct?prokey={$_REQUEST['prokey']}' 
         class='btn btn-sm btn-warning' role='button'><span class='glyphicon glyphicon-pencil'></span></a>"
    );
}
else {
    $view->h1("<small>Viewing:</small> {$product->pDescription}");
}

//Indicate the product and heading
$view->h3("<small>Created by: <strong>{$product->pCreator}</strong>, on: <strong>{$product->pCreateDate}</strong></small>");

//Determine the users permissions for control bar
$control_bar = [];
if ($server->checkPermission('viewBOM'))
    $control_bar['View/Edit BOM'] = "window.open(\"{$view->PageData['approot']}/products/bom?prokey={$_REQUEST['prokey']}\",\"_self\");";
if ($server->checkPermission('editWorkCell'))
    $control_bar['Create Work Cell'] = "window.open(\"{$view->PageData['approot']}/cells/createworkcell?prokey={$_REQUEST['prokey']}\",\"_self\");";
if ($server->checkPermission('viewWorkCell'))
    $control_bar['Work Cells'] = "window.open(\"{$view->PageData['approot']}/cells/main?action=list&prokey={$_REQUEST['prokey']}\",\"_self\");";
if (!empty($control_bar)) 
    $form->inlineButtonGroup($control_bar);


//horizontal rule for visual seperation
$view->hr();

#Product stats if allowed by 'viewProductStats' permission
if ($server->checkPermission('viewProductStats')) {
    echo "<form class='form-inline'>\n";
    echo "<input type='hidden' name='prokey' value='{$_REQUEST['prokey']}' />\n";
    echo "<strong>Date Range Lookup:</strong>\n";
    echo "<div class='input-group input-daterange'>\n";
    echo "<input class='form-control' type='text' name='begin' value='{$beginDate}' />\n";
    echo "<span class='input-group-addon'>to</span>\n";
    echo "<input class='form-control' type='text' name='end' value='{$endDate}'/>\n";
    echo "<div class='input-group-btn'>";
    echo "<button class='btn btn-default form-control' role='submit'><span class='glyphicon glyphicon-search'></span></button>\n";
    echo "</div></div></form>\n";
    echo "<hr />\n";
    echo "<ul class='list-group'>\n";
    echo "<li class='list-group-item'>Total Period Count <span class='badge'>{$product->pStats['total_count']}</span></li>\n";
    if ($product->pState == 'Active') {
        echo "<li class='list-group-item'>Total Today Count <span class='badge'>{$product->pStats['today_count']}</span></li>\n";
    }
    echo "<li class='list-group-item'>Total Period FTC <span class='badge'>{$product->pStats['total_ftc']}</span></li>\n";
    echo "</ul>\n";
}

#Currently implemented product quality control points
if ($server->checkPermission('addProduct')) {
    $products = new Products($server->pdo);
    $view->hr();
    $view->beginBtnCollapse('Show/Hide Checkpoints');
    $view->h3(
        "Current QC Points <a href='{$view->PageData['approot']}/products/qualitycheckpoints?prokey={$_REQUEST['prokey']}'
        class='btn btn-warning btn-sm' role='button'><span class='glyphicon glyphicon-pencil'></span></a>\n"
    );
    echo "<div class='table-responsive'>\n";
    echo "<table class='table'>\n";
    echo "<tr><th>Number</th><th>Checkpoint</th><th>Associated Cell</th><th>Date Implemented</th></tr>\n";
    $cnt = 1;
    foreach($products->getCheckPoints($_REQUEST['prokey']) as $row) {
        echo "<tr><td>{$cnt}</td><td>{$row['description']}</td><td>{$row['cell']}</td><td>".date('Y/m/d',strtotime($row['_date']))."</td></tr>\n";
        $cnt++;
    }
    echo "</table></div>\n";
    $view->endBtnCollapse();
}

#Product log section 
if ($server->checkPermission('viewProductLog')) {
    $edit = $server->checkPermission('editProductLog');
    $view->hr();
    $view->beginBtnCollapse('Show/Hide Log');
    if ($server->checkPermission('inspectQC')) {
        $view->h3("Production Log <a href='{$view->PageData['approot']}/products/productqc?prokey={$_REQUEST['prokey']}' 
            class='btn btn-success btn-sm' role='button'><span class='glyphicon glyphicon-plus'></span></a>");
    }
    else {
        $view->h3('Production Log');
    }
    echo "<div class='table-responsive'>\n";
    echo "<table class='table'>\n";
    echo "<tr>";
    if ($edit) {
        echo "<th>Edit</th>";
    }
    echo "<th>Sequence#</th><th>Serial#</th><th>Unit Description</th><th>Unit QC</th><th>Date</th><th>Comments</th><th>Inspector</th></tr>\n";
    foreach($product->pLog as $row) {
        echo "<tr>";
        if ($edit) {
            echo "<td><a href='{$view->PageData['approot']}/products/editlogentry?id={$row['id']}' class='btn btn-xs btn-warning' role='button'>";
            echo "<span class='glyphicon glyphicon-pencil'></span></a></td>";
        }
        echo "<td>{$row['sequence_number']}</td>";
        echo "<td>{$row['serial_number']}</td><td>{$row['misc']}</td>";
        echo "<td>{$row['ftc']}</td><td>".date('c',strtotime($row['_date']))."</td>";
        echo "<td>{$row['comments']}</td><td>{$row['inspector']}</td></tr>\n";
    }
    echo "</table></div>\n";
    $view->endBtnCollapse();
}
$view->addScrollTopBtn();
$view->footer([$view->PageData['wwwroot'].'/scripts/viewproduct.js']);