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
//include('./submenu.php'); Removed leagcy build

$server->userMustHavePermission('viewProduct');

$products = new Products($server->pdo);

$view = $server->getViewer("Production: Main");
//$view->sideDropDownMenu($submenu); Removed leagcy build
$view->h1("Products");

$form = new InlineFormWidgets($view->PageData['wwwroot'].'/scripts');

#Production Manager options
if ($server->checkPermission('addProduct')) 
    $control_panel['Add Product'] = "window.open(\"{$view->PageData['approot']}/products/addnewproduct\",\"_self\");";
if (!empty($control_panel))
    $form->inlineButtonGroup($control_panel);$view->hr();

#search bar
$form->fullPageSearchBar('product_search','Product Search');
if (!empty($_REQUEST['product_search'])) {
    if (($content = $products->searchProducts($_REQUEST['product_search'])) === false) {
        $content = "{$_REQUEST['product_search']} not found..";
    }
    if (is_array($content)) {
        echo "<div class='table-responsive'>\n<table class='table'>\n";
        echo "<tr><th>Product</th><th>Active Production</th></tr>\n";
        foreach($content as $row) {
            echo "<tr>\n";
            echo "<td><a href='{$view->PageData['approot']}/products/viewproduct?prokey={$row['product_key']}'>{$row['description']}</a></td>\n";
            echo "<td>";
            if ($row['active'] == 'true') {
                echo "Yes";
            }
            else {
                echo "No";
            }
            echo "</td>\n</tr>\n";
        }
        echo "</table>\n</div>\n";
    }
    else {
        echo $content;
    }
}

$view->footer();
