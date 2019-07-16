<?php
/* This file is part of Udata.
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
include('submenu.php');

$sds = new SDSHandler($server->pdo, $server->config['data-root']);
if (!empty($_REQUEST['delete']) && $server->checkPermission('editSDS')) {
    if ($sds->deleteSDS($_REQUEST['delete'])) {
        $server->newEndUserDialog('SDS deleted succesfully', DIALOG_SUCCESS,$server->config['application-root'].'/safety/main');
    }
    else {
        $server->newEndUserDialog('Something went wrong with the request',DIALOG_FAILURE,$server->config['application-root'].'/safety/main');
    }
}
if (!empty($_REQUEST['search_sds'])) {
    $content = $sds->searchSDS($_REQUEST['search_sds']);
}
else {
    $content = $sds->latestAdded();
}

$view = $server->getViewer('Safety Data Sheets');
$view->sideDropDownMenu($submenu);
?>
<div>
    <h1>Hazardous Communication</h1>
    <form>
        <div class="input-group">
            <input type="text" class="form-control" placeholder="SDS Search" name='search_sds'>
            <div class="input-group-btn">
            <button class="btn btn-default" type="submit">
                <span class="glyphicon glyphicon-search"></span>
            </button>
            </div>
        </div>
    </form>
</div>
<div>
    <h2>
        <?php 
            if (empty($_REQUEST)) {
                echo 'Latest Added';
            }
            else {
                echo 'Search Results';
            }
            if ($server->checkPermission('addSDS')) {
                echo "&#160;<a href='{$server->config['application-root']}/safety/addsds' class='btn btn-md btn-info' role='button'>
                        <span class='glyphicon glyphicon-plus'></span>&#160;Add SDS</a>";
            }
         ?>
    </h2>
    <div class='table-responsive'>
        <table class='table'>
            <tr>
                <th>Product</th>
                <th>Distributor</th>
                <?php if ($server->checkPermission('editSDS')) {echo "<th>Edit</th>";}?>
            </tr>
            <?php 
            foreach($content as $row) {
                echo "<tr>
                        <td>
                            <span class='glyphicon glyphicon-file'></span>&#160;
                            <a href='{$server->config['application-root']}/data/files?dis=inline&file=".urlencode($row['file'])."' download='{$row['file']}'>
                                {$row['name']}
                            </a>
                        </td>
                        <td>
                            {$row['dist']}
                        </td>";
                if ($server->checkPermission('editSDS')) {
                    echo "<td>
                            <a href='?delete={$row['id']}:{$row['fid']}' class='btn btn-danger' role='button'>
                                <span class='glyphicon glyphicon-trash'></span>
                            </a>
                        </td>";
                }
                echo "</tr>\n";
            }
            ?>
        </table>
    </div>
</div>
<?php
$view->addScrollTopBtn();
$view->footer();