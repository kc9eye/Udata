<?php
   /*
   Copyright 2009-2014 Paul W.Lane <kc9eye@gmail.com>
   This file is part of chicago-assy intranet.

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; version 2
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
$view = $server->getViewer("Legacy Products");
$view->PageData['headinserts'] = [
    '<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>',
    '<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.11.4/themes/vader/jquery-ui.css" />'
];
$view->sideDropDownMenu($submenu);
?>
<script type='text/javascript'>
   function openDetails (type,unit) {
     if ($("#details").css("display") == "none") {
        $.post(
           "<?php echo $server->config['application-root']."/products/legacy/production_lib/details";?>",
           {table:type,id:unit},
           function(data) {
              $("#details").css("display","block");
              $("#details").html(data);
              });
        }
     else {
        $("#details").fadeOut("slow");
        }
     }
     /*
   function drawCharts() {
      var options = {title: 'First Time Capability'};
      // These two lines get the data from the database. Needs to be redisgned with php json_encode, but not now.
      //-------------------------------------------------------------------------------------------------------------
      var truckData = google.visualization.arrayToDataTable(<?php echo wkly_cap_chart_google_data("trk_prod");?>);
      var trailerData = google.visualization.arrayToDataTable(<?php echo wkly_cap_chart_google_data("trlr_prod");?>);

      // These lines instantiate the charts in the relative elements.
      //--------------------------------------------------------------------------------------
      var ttChart = new google.visualization.LineChart(document.getElementById("tt_chart"));
      //var rvChart = new google.visualization.LineChart(document.getElementById("rv_chart"));
      var dcChart = new google.visualization.LineChart(document.getElementById("dc_chart"));
      //var mtChart = new google.visualization.LineChart(document.getElementById("mt_chart"));
      //var rtChart = new google.visualization.LineChart(document.getElementById("rt_chart"));
      var atChart = new google.visualization.LineChart(document.getElementById("at_chart"));
      //var tdChart = new google.visualization.LineChart(document.getElementById("td_chart"));
      //var jhChart = new google.visualization.LineChart(document.getElementById("jh_chart"));
      //var avChart = new google.visualization.LineChart(document.getElementById("av_chart"));
      //var uvChart = new google.visualization.LineChart(document.getElementById("uv_chart"));
      //var aoChart = new google.visualization.LineChart(document.getElementById("ao_chart"));
      //var dbChart = new google.visualization.LineChart(document.getElementById('db_chart'));

      // These lines actually draw the charts in the elements.
      //-------------------------------------------------------------------------------------
      ttChart.draw(truckData,options);
      //rvChart.draw(trailerData,options);
      dcChart.draw(truckData,options);
      //avChart.draw(trailerData,options);
      //mtChart.draw(trailerData,options);
      //rtChart.draw(trailerData,options);
      atChart.draw(trailerData,options);
      //tdChart.draw(trailerData,options);
      //jhChart.draw(truckData,options);
      //uvChart.draw(trailerData,options);
      //aoChart.draw(trailerData, options);
      //dbChart.draw(trailerData, options);
      }

   google.load('visualization', '1', {packages:['corechart']});
*/
   $(document).ready(function(){
   	  //drawCharts();
      $("#tabs").tabs();
      $("#tt_prod_tabs").tabs();
      $("#dc_prod_tabs").tabs();
      //$("#av_prod_tabs").tabs();
      //$("#rv_prod_tabs").tabs();
      //$("#mt_prod_tabs").tabs();
      //$("#rt_prod_tabs").tabs();
      $("#at_prod_tabs").tabs();
      //$("#td_prod_tabs").tabs();
      //$("#jh_prod_tabs").tabs();
      //$("#uv_prod_tabs").tabs();
      //$("#ao_prod_tabs").tabs();
      //$("#db_prod_tabs").tabs();
      });
</script>
<div id="right_data_display">
   <div id="tabs" style="overflow:hidden">

<!-- Tabs top menu tabs ------------------------------------>
      <ul>
         <li><a href="#tt_data">TT Figures</a></li>
<!--         <li><a href="#jh_data">JH Figures</a></li> -->
         <li><a href="#dc_data">DC Figures</a></li>
<!--         <li><a href="#rv_data">RV Figures</a></li> -->
<!--         <li><a href="#av_data">AV Figures</a></li> -->
<!--         <li><a href="#mt_data">MT Figures</a></li> -->
<!--         <li><a href="#rt_data">RT Figures</a></li> -->
         <li><a href="#at_data">AT Figures</a></li>
<!--	     <li><a href="#td_data">TD Figures</a></li> -->
<!--         <li><a href="#uv_data">UV Figures</a></li> -->
<!--         <li><a href="#ao_data">AO Figures</a></li> -->
<!--         <li><a href="#db_data">DB Figures</a></li> -->
      </ul>
<!---------------------------------------------------------->
<!-- TT truck Tab ------------------------------------------>
      <div id="tt_data">
         <?php
            $total = sql_total_prod("2018","TT","J","trk_prod");
            $today = sql_today_prod("2018","TT","J","trk_prod");
         ?>
         <table>
            <tr>
                  <td><img src="/img/TTlarge.png" alt="TT" /></td>
                  <td>
                     <table style="margin-left:5px;border:3px solid rgb(0,0,0);border-radius:5px;">
                        <tr><td colspan="2" style="text-align:center"><button onclick="openDetails('stats','holder')">Show Stats.</button></td></tr>
                        <tr><th colspan="2">2018 TT-J</th></tr>
                        <tr><th style="text-align:right;">Total to Date:</th><td><?php echo $total['total'];?></td></tr>
                        <tr><th style="text-align:right;">Total Capability:</th><td><?php echo round($total['capability'],2);?></td></tr>
                        <tr><th style="text-align:right;">Today Total:</th><td><?php echo $today['total'];?></td></tr>
                        <tr><th style="text-align:right;">Today's Capability</th><td><?php echo round($today['capability'],2);?></td></tr>
                     </table>
                  </td>
                  <td>
                  <div id="tt_chart" style="width:400px;height:200px;border:3px solid rgb(0,0,0);border-radius:5px;"></div>
                  </td>
            </tr>
         </table>
         <div id="tt_prod_tabs">
            <ul>
               <li><a href="#today">Today's Built</a></li>
               <li><a href="#total">Total Built</a></li>
            </ul>
            <div id="today">
               <?php echo sql_today_trk_prod("2018","TT","J");?>
            </div>
            <div id="total">
               <?php echo sql_total_trk_prod("2018","TT","J");?>
            </div>
         </div>
      </div>
<!-------------------------------------------------------------->
<!-- JH truck Tab ---------------------------------------------->
<!------------------------------------------------------------      
    <div id="jh_data">
         <?php
            //$total = sql_total_prod("2018","JH","G","trk_prod");
            //$today = sql_today_prod("2018","JH","G","trk_prod");
         ?>
         <table>
            <tr>
                  <td><img src="/img/JHlarge.png" alt="JH" /></td>
                  <td>
                     <table style="margin-left:5px;border:3px solid rgb(0,0,0);border-radius:5px;">
                        <tr><td colspan="2" style="text-align:center"><button onclick="openDetails('stats','holder')">Show Stats.</button></td></tr>
                        <tr><th colspan="2">2016 JH-F</th></tr>
                        <tr><th style="text-align:right;">Total to Date:</th><td><?php //echo $total['total'];?></td></tr>
                        <tr><th style="text-align:right;">Total Capability:</th><td><?php //echo round($total['capability'],2);?></td></tr>
                        <tr><th style="text-align:right;">Today Total:</th><td><?php //echo $today['total'];?></td></tr>
                        <tr><th style="text-align:right;">Today's Capability</th><td><?php //echo round($today['capability'],2);?></td></tr>
                     </table>
                  </td>
                  <td>
                  <div id="jh_chart" style="width:400px;height:200px;border:3px solid rgb(0,0,0);border-radius:5px;"></div>
                  </td>
            </tr>
         </table>
         <div id="jh_prod_tabs">
            <ul>
               <li><a href="#today">Today's Built</a></li>
               <li><a href="#total">Total Built</a></li>
            </ul>
            <div id="today">
               <?php //echo sql_today_trk_prod("2018","JH","G");?>
            </div>
            <div id="total">
               <?php //echo sql_total_trk_prod("2018","JH","G");?>
            </div>
         </div>
      </div>
<!-------------------------------------------------------------->
<!------------------ DC TRUCK Tab ------------------------------>
      <div id="dc_data">
         <?php
            $total = sql_total_prod("2018","DC","X","trk_prod","1");
            $today = sql_today_prod("2018","DC","X","trk_prod","1");
         ?>
         <table>
            <tr>
               <td><img src="/img/DClarge.png" alt="DC" /></td>
               <td>
                  <table style="margin-left:5px;border:3px solid rgb(0,0,0);border-radius:5px;">
                     <tr><td colspan="2" style="text-align:center"><button onclick="openDetails('stats','holder')">Show Stats.</button></td></tr>
                     <tr><th colspan="2">2018 DC-J</th></tr>
                     <tr><th style="text-align:right;">Total to Date:</th><td><?php echo $total['total'];?></td></tr>
                     <tr><th style="text-align:right;">Total Capability:</th><td><?php echo round($total['capability'],2);?></td></tr>
                     <tr><th style="text-align:right;">Today Total:</th><td><?php echo $today['total'];?></td></tr>
                     <tr><th style="text-align:right;">Today's Capability</th><td><?php echo round($today['capability'],2);?></td></tr>
                  </table>
               </td>
               <td>
                  <div id="dc_chart" style="width:400px;height:200px;border:3px solid rgb(0,0,0);border-radius:5px;"></div>
               </td>
            </tr>
         </table>
         <div id="dc_prod_tabs">
            <ul>
               <li><a href="#today">Today's Built</a></li>
               <li><a href="#total">Total Built</a></li>
            </ul>
            <div id="today">
               <?php echo sql_today_trk_prod("2018","DC","X","1");?>
            </div>
            <div id="total">
               <?php echo sql_total_trk_prod("2018","DC","X","1");?>
            </div>
         </div>
      </div>
<!---------------------------------------------------------------->
<!--           RV TRAILER TAB                                   
      <div id="rv_data">
         <?php
            //$total = sql_total_prod("2016","RV","B","trlr_prod");
           // $today = sql_today_prod("2016","RV","B","trlr_prod");
         ?>
         <table>
            <tr>
               <td><img src="/img/RVlarge.png" alt="rv" /></td>
               <td>
                  <table style="margin-left:5px;border:3px solid rgb(0,0,0);border-radius:5px;">
                     <tr><td colspan="2" style="text-align:center"><button onclick="openDetails('stats','holder')">Show Stats.</button></td></tr>
                     <tr><th colspan="2">2016 RV-B</th></tr>
                     <tr><th style="text-align:right;">Total to Date:</th><td><?php //echo $total['total'];?></td></tr>
                     <tr><th style="text-align:right;">Total Capability:</th><td><?php //echo round($total['capability'],2);?></td></tr>
                     <tr><th style="text-align:right;">Today Total:</th><td><?php //echo $today['total'];?></td></tr>
                     <tr><th style="text-align:right;">Today's Capability</th><td><?php //echo round($today['capability'],2);?></td></tr>
                  </table>
               </td>
               <td>
                  <div id="rv_chart" style="width:400px;height:200px;border:3px solid rgb(0,0,0);border-radius:5px;"></div>
               </td>
            </tr>
         </table>
         <div id="rv_prod_tabs">
            <ul>
               <li><a href="#today">Today's Built</a></li>
               <li><a href="#total">Total Built</a></li>
            </ul>
            <div id="today">
               <?php //echo sql_today_trlr_prod("2016","RV","B");?>
            </div>
            <div id="total">
               <?php //echo sql_total_trlr_prod("2016","RV","B");?>
            </div>
         </div>
      </div>
<!------------------------------------------------------------------->
<!--           MT TRAILER TAB
      <div id="mt_data">
         <?php
            //$total = sql_total_prod("2014","MT","B","trlr_prod");
            //$today = sql_today_prod("2014","MT","B","trlr_prod");
         ?>
         <table>
            <tr>
               <td><img src="/img/MTlarge.png" alt="rv" /></td>
               <td>
                  <table style="margin-left:5px;border:3px solid rgb(0,0,0);border-radius:5px;">
                     <tr><td colspan="2" style="text-align:center"><button onclick="openDetails('stats','holder')">Show Stats.</button></td></tr>
                     <tr><th colspan="2">2014 MT-B</th></tr>
                     <tr><th style="text-align:right;">Total to Date:</th><td><?php //echo $total['total'];?></td></tr>
                     <tr><th style="text-align:right;">Total Capability:</th><td><?php //echo round($total['capability'],2);?></td></tr>
                     <tr><th style="text-align:right;">Today Total:</th><td><?php //echo $today['total'];?></td></tr>
                     <tr><th style="text-align:right;">Today's Capability</th><td><?php //echo round($today['capability'],2);?></td></tr>
                  </table>
               </td>
               <td>
                  <div id="mt_chart" style="width:400px;height:200px;border:3px solid rgb(0,0,0);border-radius:5px;"></div>
               </td>
            </tr>
         </table>
         <div id="mt_prod_tabs">
            <ul>
               <li><a href="#today">Today's Built</a></li>
               <li><a href="#total">Total Built</a></li>
            </ul>
            <div id="today">
               <?php //echo sql_today_trlr_prod("2014","MT","B");?>
            </div>
            <div id="total">
               <?php //echo sql_total_trlr_prod("2014","MT","B");?>
            </div>
         </div>
      </div>
<!------------------------------------------------------------------->
<!--           RT TRAILER TAB                                   
      <div id="rt_data">
         <?php
            //$total = sql_total_prod("2015","RT","C","trlr_prod");
            //$today = sql_today_prod("2015","RT","C","trlr_prod");
         ?>
         <table>
            <tr>
               <td><img src="/img/RTlarge.png" alt="rt" /></td>
               <td>
                  <table style="margin-left:5px;border:3px solid rgb(0,0,0);border-radius:5px;">
                     <tr><td colspan="2" style="text-align:center"><button onclick="openDetails('stats','holder')">Show Stats.</button></td></tr>
                     <tr><th colspan="2">2015 RT-C</th></tr>
                     <tr><th style="text-align:right;">Total to Date:</th><td><?php //echo $total['total'];?></td></tr>
                     <tr><th style="text-align:right;">Total Capability:</th><td><?php //echo round($total['capability'],2);?></td></tr>
                     <tr><th style="text-align:right;">Today Total:</th><td><?php //echo $today['total'];?></td></tr>
                     <tr><th style="text-align:right;">Today's Capability</th><td><?php //echo round($today['capability'],2);?></td></tr>
                  </table>
               </td>
               <td>
                  <div id="rt_chart" style="width:400px;height:200px;border:3px solid rgb(0,0,0);border-radius:5px;"></div>
               </td>
            </tr>
         </table>
         <div id="rt_prod_tabs">
            <ul>
               <li><a href="#today">Today's Built</a></li>
               <li><a href="#total">Total Built</a></li>
            </ul>
            <div id="today">
               <?php //echo sql_today_trlr_prod("2015","RT","C");?>
            </div>
            <div id="total">
               <?php //echo sql_total_trlr_prod("2015","RT","C");?>
            </div>
         </div>
      </div>
<!------------------------------------------------------------------->
<!--           AT TRAILER TAB                                      
                                                               
      <div id="at_data">
         <?php
            //$total = sql_total_prod("2018","AT","R","trlr_prod");
            //$today = sql_today_prod("2018","AT","R","trlr_prod");
         ?>
         <table>
            <tr>
               <td><img src="/img/ATlarge.png" alt="at" /></td>
               <td>
                  <table style="margin-left:5px;border:3px solid rgb(0,0,0);border-radius:5px;">
                     <tr><td colspan="2" style="text-align:center"><button onclick="openDetails('stats','holder')">Show Stats.</button></td></tr>
                     <tr><th colspan="2">2018 AT-R</th></tr>
                     <tr><th style="text-align:right;">Total to Date:</th><td><?php //echo $total['total'];?></td></tr>
                     <tr><th style="text-align:right;">Total Capability:</th><td><?php //echo round($total['capability'],2);?></td></tr>
                     <tr><th style="text-align:right;">Today Total:</th><td><?php //echo //$today['total'];?></td></tr>
                     <tr><th style="text-align:right;">Today's Capability</th><td><?php //echo //round($today['capability'],2);?></td></tr>
                  </table>
               </td>
               <td>
                  <div id="at_chart" style="width:400px;height:200px;border:3px solid rgb(0,0,0);border-radius:5px;"></div>
               </td>
            </tr>
         </table>
         <div id="at_prod_tabs">
            <ul>
               <li><a href="#today">Today's Built</a></li>
               <li><a href="#total">Total Built</a></li>
            </ul>
            <div id="today">
               <?php //echo sql_today_trlr_prod("2018","AT","R");?>
            </div>
            <div id="total">
               <?php //echo sql_total_trlr_prod("2018","AT","R");?>
            </div>
         </div>
      </div>
<!-------------------------------------------------------------------->
<!------------------------TD TRAILER TAB --------------------------
      <div id="td_data">
         <?php
            #$total = sql_total_prod("2016","TD","W","trlr_prod");
            #$today = sql_today_prod("2016","TD","W","trlr_prod");
         ?>
         <table>
            <tr>
               <td><img src="/img/TDlarge.png" alt="td" /></td>
               <td>
                  <table style="margin-left:5px;border:3px solid rgb(0,0,0);border-radius:5px;">
                     <tr><td colspan="2" style="text-align:center"><button onclick="openDetails('stats','holder')">Show Stats.</button></td></tr>
                     <tr><th colspan="2">2015 TD-W</th></tr>
                     <tr><th style="text-align:right;">Total to Date:</th><td><?php #echo $total['total'];?></td></tr>
                     <tr><th style="text-align:right;">Total Capability:</th><td><?php #echo round($total['capability'],2);?></td></tr>
                     <tr><th style="text-align:right;">Today Total:</th><td><?php #echo $today['total'];?></td></tr>
                     <tr><th style="text-align:right;">Today's Capability</th><td><?php #echo round($today['capability'],2);?></td></tr>
                  </table>
               </td>
               <td>
                  <div id="td_chart" style="width:400px;height:200px;border:3px solid rgb(0,0,0);border-radius:5px;"></div>
               </td>
            </tr>
         </table>
         <div id="td_prod_tabs">
            <ul>
               <li><a href="#today">Today's Built</a></li>
               <li><a href="#total">Total Built</a></li>
            </ul>
            <div id="today">
               <?php #echo sql_today_trlr_prod("2016","TD","W");?>
            </div>
            <div id="total">
               <?php #echo sql_total_trlr_prod("2016","TD","W");?>
            </div>
         </div>
      </div>
<!------------------------------------------------------------------->
<!--           AV TRAILER TAB                                                                         
      <div id="av_data">
         <?php
#            $total = sql_total_prod("2016","AV","N","trlr_prod");
#            $today = sql_today_prod("2016","AV","N","trlr_prod");
         ?>
         <table>
            <tr>
               <td><img src="/img/AVlarge.png" alt="rt" /></td>
               <td>
                  <table style="margin-left:5px;border:3px solid rgb(0,0,0);border-radius:5px;">
                     <tr><td colspan="2" style="text-align:center"><button onclick="openDetails('stats','holder')">Show Stats.</button></td></tr>
                     <tr><th colspan="2">2016 AV-N</th></tr>
                     <tr><th style="text-align:right;">Total to Date:</th><td><?php #echo $total['total'];?></td></tr>
                     <tr><th style="text-align:right;">Total Capability:</th><td><?php #echo round($total['capability'],2);?></td></tr>
                     <tr><th style="text-align:right;">Today Total:</th><td><?php #echo $today['total'];?></td></tr>
                     <tr><th style="text-align:right;">Today's Capability</th><td><?php #echo round($today['capability'],2);?></td></tr>
                  </table>
               </td>
               <td>
                  <div id="av_chart" style="width:400px;height:200px;border:3px solid rgb(0,0,0);border-radius:5px;"></div>
               </td>
            </tr>
         </table>
         <div id="av_prod_tabs">
            <ul>
               <li><a href="#today">Today's Built</a></li>
               <li><a href="#total">Total Built</a></li>
            </ul>
            <div id="today">
               <?php #echo sql_today_trlr_prod("2016","AV","N");?>
            </div>
            <div id="total">
               <?php #echo sql_total_trlr_prod("2016","AV","N");?>
            </div>
         </div>
      </div>
<!------------------------------------------------------------------->
<!--           UV TRAILER TAB                                     
      <div id="uv_data">
         <?php
            //$total = sql_total_prod("2017","UV","C","trlr_prod");
            //$today = sql_today_prod("2017","UV","C","trlr_prod");
         ?>
         <table>
            <tr>
               <td><img src="/img/UVlarge.png" alt="rt" /></td>
               <td>
                  <table style="margin-left:5px;border:3px solid rgb(0,0,0);border-radius:5px;">
                     <tr><td colspan="2" style="text-align:center"><button onclick="openDetails('stats','holder')">Show Stats.</button></td></tr>
                     <tr><th colspan="2">2017 UV-C</th></tr>
                     <tr><th style="text-align:right;">Total to Date:</th><td><?php //echo $total['total'];?></td></tr>
                     <tr><th style="text-align:right;">Total Capability:</th><td><?php //echo round($total['capability'],2);?></td></tr>
                     <tr><th style="text-align:right;">Today Total:</th><td><?php //echo $today['total'];?></td></tr>
                     <tr><th style="text-align:right;">Today's Capability</th><td><?php //echo round($today['capability'],2);?></td></tr>
                  </table>
               </td>
               <td>
                  <div id="uv_chart" style="width:400px;height:200px;border:3px solid rgb(0,0,0);border-radius:5px;"></div>
               </td>
            </tr>
         </table>
         <div id="uv_prod_tabs">
            <ul>
               <li><a href="#today">Today's Built</a></li>
               <li><a href="#total">Total Built</a></li>
            </ul>
            <div id="today">
               <?php //echo sql_today_trlr_prod("2017","UV","C");?>
            </div>
            <div id="total">
               <?php //echo sql_total_trlr_prod("2017","UV","C");?>
            </div>
         </div>
      </div>
<!---------------------------------------------------------------------->
<!--           AO TRAILER TAB                                         -->
<!----------------------------------------------------------------------
      <div id="ao_data">
      <?php
            //$total = sql_total_prod("2018","AO","L","trlr_prod");
            //$today = sql_today_prod("2018","AO","L","trlr_prod");
         ?>
         <table>
            <tr>
               <td><img src="/img/AOlarge.png" alt="rt" /></td>
               <td>
                  <table style="margin-left:5px;border:3px solid rgb(0,0,0);border-radius:5px;">
                     <tr><td colspan="2" style="text-align:center"><button onclick="openDetails('stats','holder')">Show Stats.</button></td></tr>
                     <tr><th colspan="2">2018 AO-L</th></tr>
                     <tr><th style="text-align:right;">Total to Date:</th><td><?php //echo $total['total'];?></td></tr>
                     <tr><th style="text-align:right;">Total Capability:</th><td><?php //echo round($total['capability'],2);?></td></tr>
                     <tr><th style="text-align:right;">Today Total:</th><td><?php //echo $today['total'];?></td></tr>
                     <tr><th style="text-align:right;">Today's Capability</th><td><?php //echo round($today['capability'],2);?></td></tr>
                  </table>
               </td>
               <td>
                  <div id="ao_chart" style="width:400px;height:200px;border:3px solid rgb(0,0,0);border-radius:5px;"></div>
               </td>
            </tr>
         </table>
         <div id="ao_prod_tabs">
            <ul>
               <li><a href="#today">Today's Built</a></li>
               <li><a href="#total">Total Built</a></li>
            </ul>
            <div id="today">
               <?php //echo sql_today_trlr_prod("2018","AO","L");?>
            </div>
            <div id="total">
               <?php //echo sql_total_trlr_prod("2018","AO","L");?>
            </div>
         </div>
      </div>
<!-------------------------------------------------------------------->
<!-- DB Trailer Division                                            -->
<!------------------------------------------------------------------
      <div id="db_data">
      <?php
            //$total = sql_total_prod("2018","DB","A","trlr_prod");
            //$today = sql_today_prod("2018","DB","A","trlr_prod");
         ?>
         <table>
            <tr>
               <td><img src="/img/UBlarge.png" alt="at" /></td>
               <td>
                  <table style="margin-left:5px;border:3px solid rgb(0,0,0);border-radius:5px;">
                     <tr><td colspan="2" style="text-align:center"><button onclick="openDetails('stats','holder')">Show Stats.</button></td></tr>
                     <tr><th colspan="2">2018 DB-A</th></tr>
                     <tr><th style="text-align:right;">Total to Date:</th><td><?php //echo $total['total'];?></td></tr>
                     <tr><th style="text-align:right;">Total Capability:</th><td><?php //echo round($total['capability'],2);?></td></tr>
                     <tr><th style="text-align:right;">Today Total:</th><td><?php //echo $today['total'];?></td></tr>
                     <tr><th style="text-align:right;">Today's Capability</th><td><?php //echo round($today['capability'],2);?></td></tr>
                  </table>
               </td>
               <td>
                  <div id="db_chart" style="width:400px;height:200px;border:3px solid rgb(0,0,0);border-radius:5px;"></div>
               </td>
            </tr>
         </table>
         <div id="db_prod_tabs">
            <ul>
               <li><a href="#today">Today's Built</a></li>
               <li><a href="#total">Total Built</a></li>
            </ul>
            <div id="today">
               <?php //echo sql_today_trlr_prod("2018","DB","A");?>
            </div>
            <div id="total">
               <?php //echo sql_total_trlr_prod("2018","DB","A");?>
            </div>
         </div>
      </div>
<!------------------------------------------------------------------->
<!------------------------------------------------------------------->
<!---------------------------------------------------------------- -->
   </div>
<!--                 END TABS DIVISION                            -->
   <div id="build_results"></div>
</div>
<?php 
$view->addScrollTopBtn();
$view->footer();