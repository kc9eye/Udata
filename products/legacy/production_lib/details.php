<?php
   /*
   Copyright 2009-2014 Paul W.Lane <kc9eye@gmail.com>
   This file is part of chicago-assy intranet.

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
   */

require_once(dirname(__DIR__).'/legacy_init.php');
require_once('columns_relation.php');
/*
    This needs to change for each run
*********************************************/
$TRK_YEAR = "2018";
$TRK_MODEL = "TT";
$TRK_SUFFIX = "J";
$TRK_RUN = "1";

$TRL_YEAR = "2018";
$TRL_MODEL = "AT";
$TRL_SUFFIX = "R";
$TRL_RUN = "1";


$dbsock = pg_connect(__DB__) or js_err_msg("Can't contact database!","details.php","line 24");
switch($_REQUEST['table']) {
   case "truck":
      $COLS = $TRK_COLS;
      $QUERY = "SELECT * FROM trk_prod WHERE id = '{$_POST['id']}'";
      $UNIT = pg_fetch_assoc(pg_query($dbsock,$QUERY));
      break;
   case "trailer":
      $COLS = $TRL_COLS;
      $QUERY = "SELECT * FROM trlr_prod WHERE id = '{$_POST['id']}'";
      $UNIT = pg_fetch_assoc(pg_query($dbsock,$QUERY));
      break;
   case "stats":
      $sun = last_sunday();
      $fri = date("Y-m-d",strtotime("{$sun} +5 days"));
      $sql = "SELECT avg(capability) FROM trk_prod WHERE _date BETWEEN '{$sun}' AND '{$fri}'";
      $TRK_STATS = pg_fetch_result(pg_query($dbsock,$sql),0,0);
      $sql = "SELECT avg(capability) FROM trlr_prod WHERE _date BETWEEN '{$sun}' AND '{$fri}'";
      $TRL_STATS = pg_fetch_result(pg_query($dbsock,$sql),0,0);
      foreach($TRK_COLS as $col => $display) {
         $QUERY = "
               with trucks as (
                  select * from trk_prod where model_year='{$TRK_YEAR}' and model_prefix='{$TRK_MODEL}' and model_suffix='{$TRK_SUFFIX}' AND run_num = '{$TRK_RUN}'
                  )
               SELECT
                   (SELECT CAST(count({$col}) as decimal) FROM trucks WHERE {$col} = 't' ) /
                   (SELECT CAST(count({$col}) as decimal) FROM trucks WHERE {$col} IS NOT NULL) * 100";
         $TRK_PNT[$display] = pg_fetch_result(pg_query($dbsock,$QUERY),0,0);
         }
      foreach($TRL_COLS as $col => $display) {
         $QUERY = "SELECT
                   (SELECT CAST(count({$col}) as decimal) FROM trlr_prod WHERE {$col} = 't' AND model_year = '{$TRL_YEAR}' AND model_prefix = '{$TRL_MODEL}') /
                   (SELECT CAST(count({$col}) as decimal) FROM trlr_prod WHERE {$col} IS NOT NULL AND model_year = '{$TRL_YEAR}' AND model_prefix = '{$TRL_MODEL}') * 100";
         $TRL_PNT[$display] = pg_fetch_result(pg_query($dbsock,$QUERY),0,0);
         }
      break;
   }

$REPORTER = sql_reporter_name($UNIT['reporter_id']);
?>

<div style="
   overflow:scroll;
   position:absolute;
   left:25%;
   top:15%;
   width:50%;
   max-height:75%;
   padding:5px;
   color:rgb(0,0,0);
   font-family:arial sans-serif serif;
   text-align:left;
   background-color:rgb(255,255,255);
   border-radius:5px;">
   <p style="font-size:large;font-weight:bold;text-align:right;">
      <span
         onmouseover="
            $(this).css('cursor','pointer');
            $(this).css('color','red')"
         onmouseout="$(this).css('color','blue');"
         onclick="openDetails()"
         style="color:blue;">
         |X| Close Details
      </span>
   </p>
   <table style="
      margin-left:auto;
      margin-right:auto;
      vertical-align:middle;
      min-width:50%"
      border="1">
<?php
   switch($_POST['table']) {
      case "stats":
?>
            <tr><th>Previous Week Truck FTC:</th><td><?php echo round($TRK_STATS,3);?>%</td></tr>
            <tr><th>Previous Week Trailer FTC:</th><td><?php echo round($TRL_STATS,3);?>%</td></tr>
            <tr><td colspan="2"><h3>Truck FTC (per checkpoint)</h3></td></tr>
<?php
   foreach($TRK_PNT as $display => $x) {
      echo "<tr><th style='text-align:right'>{$display}:</th><td>".round($x,3)."%</td></tr>";
      }
?>
            <tr><td colspan="2"><h3>Trailer FTC (per checkpoint)</h3></td></tr>
<?php
   foreach($TRL_PNT as $display => $x) {
      echo "<tr><th style='text-align:right'>{$display}:</th><td>".round($x,3)."%</td></tr>";
      }
?>
            </table>
<?php
         die();
         break;
     default:
?>
            <tr><th style="text-align:left" colspan="2"><?php echo $REPORTER;?> says:</th></tr>
<?php
         foreach($COLS as $col => $display) {
            if ($UNIT[$col] == 'f') {
               $comment = $col."_com";
               echo "<tr><th style='text-align:right'>Defect in:</th><th>{$display}</th></tr>\n";
               echo "<tr><td colspan='2' style='text-align:left'>".nl2br(htmlspecialchars($UNIT[$comment]))."</td></tr>\n";
               }
            }
?>
</table>
</div>
<?php
      break;
      }
?>
