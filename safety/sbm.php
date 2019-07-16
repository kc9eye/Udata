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
include('submenu.php');

$sbm = new SafetyBoardMinutes($server->pdo, $server->config['data-root']);
$edit = $server->checkPermission('editSBM');

if (!empty($_REQUEST) && $edit) {
    switch($_REQUEST['action']) {
        case 'add':
            $server->processingDialog([$sbm,'addNewSBMFile'],[$_REQUEST], $server->config['application-root'].'/safety/sbm');
        break;
        case 'delete':
            $server->processingDialog([$sbm,'deleteSBMFileEntry'],[$_REQUEST], $server->config['application-root'].'/safety/sbm');
        break;
        default:
            $server->processingDialog(function(){true;});
        break;
    }
}

$view = $server->getViewer('Safety Board Minutes');
$view->sideDropDownMenu($submenu);

if ($edit) {
?>
<strong>Upload Safety Meeting Minutes</strong>
<form class='form-inline' method='post' enctype='multipart/form-data'>
    <input type='hidden' name='action' value='add' />
    <input type='hidden' name='uid' value='<?php echo $server->security->secureUserID;?>' />
    <div class='form-group'>
        <label for='date'>Date:</label>
        <input type='text' id='date' name='date' class='form-control' />
    </div>
    <div class='form-group'>
        <div class='input-group'>
            <label class='input-group-btn'>
                <span class='btn btn-info'>
                    Browse&hellip;
                    <input id='files' type='file' name='<?php echo FileIndexer::UPLOAD_NAME;?>' 
                    style='display:none;' />
                </span>
            </label>
            <input type="text" class="form-control" readonly>
        </div>
    </div>
    <div class='form-group'>
        <button type='submit' class='btn btn-default form-control'>Upload</button>
    </div>
</form>
<script src='https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/additional-methods.min.js'></script>
<script src='<?php echo $view->PageData['wwwroot'];?>/scripts/btstrapfileinputhack.js'></script>
<script>
    $(document).ready(function(){
        $('form').validate({
            rules:{
                date:{
                    required: true,
                    dateISO: true
                },
                <?php echo FileIndexer::UPLOAD_NAME;?>: {
                    required: true
                }
            }
        });
    });
</script>
<hr />
<?php
}
?>
<div class='row'>
    <h2>Meeting Minute Files</h2>
    <div class='col-md-10 col-xs-12'>
        <div class='table-responsive'>
            <table class='table'>
                <tr><th>Safety Board Meeting Minutes Files</th><?php if ($edit){echo "<th>Remove</th>";}?></tr>
                <?php
                    foreach($sbm->getListing() as $row) {
                        echo "<tr>
                            <td>
                                <span class='glyphicon glyphicon-file'></span>
                                <a href='{$view->PageData['approot']}/data/files?file={$row['file']}'>{$row['meeting_date']} Meeting</a>
                            </td>";
                        if ($edit) {
                            echo "<td><a href='?action=delete&ref={$row['id']}:{$row['fid']}' class='btn btn-danger' role='button'>
                                <span class='glyphicon glyphicon-trash'></span>
                            </td>";
                        }
                        echo "</tr>\n";
                    }
                ?>
            </table>
        </div>
    </div>
</div>
<?php
$view->addScrollTopBtn();
$view->footer();

