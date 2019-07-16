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

   // HTML error handling
   // returns html
   function js_err_msg() {
      $fh = fopen(__VAR__."/http_error.log","a");
      $args = func_get_args();
      $t = count($args);
      $str = "";
      for($cnt=0;$cnt<=($t - 1);$cnt++) {
         $str .= " <-{$args[$cnt]}";
         }
      fwrite($fh,date("c")." [{$_SERVER['REMOTE_ADDR']}]:{$_SERVER['REQUEST_URI']} {$str}\n");
      fclose($fh);
      echo "<script type='text/javascript'>";
      echo "alert(\"ERROR:\\n";
      foreach($args as $arg) {
         echo "<-{$arg}\\n";
         }
      echo "SEVERITY: FATAL\");";
      echo "window.open('/etc/error.php','_self');";
      echo "</script>";
      die();
      }

   // sql_total_prod (string $year, string $prefix, string suffix, string $table [,string $run_num])
   // returns array.
   function sql_total_prod () {
      $args = func_get_args();
      $dbsock = pg_connect(__DB__) or js_err_msg("Can't connect to database","sql_total_prod()");
      $sql = "
         SELECT count(*) as total, avg(capability) as capability
         FROM {$args[3]}
         WHERE model_year = '{$args[0]}'
         AND model_prefix = '{$args[1]}'
         AND model_suffix = '{$args[2]}'
         ";
      if (isset($args[4])) {
         $sql .= "AND run_num = '{$args[4]}'";
         }
      $result = pg_query($dbsock, $sql);
      if (pg_result_status($result) != PGSQL_TUPLES_OK) {
         js_err_msg("No tuples returned","sql_total_prod");
         }
      return pg_fetch_assoc($result);
      }

   // sql_today_prod (string $year, string $prefix, string $suffix, string $table [, string $run_num])
   // returns array
   function sql_today_prod () {
      $args = func_get_args();
      $dbsock = pg_connect(__DB__) or js_err_msg("Can't connect to database","sql_today_prod()");
      $sql = "
         SELECT count(*) as total, avg(capability) as capability
         FROM {$args[3]}
         WHERE model_year = '{$args[0]}'
         AND model_prefix = '{$args[1]}'
         AND model_suffix = '{$args[2]}'
         AND date_trunc('day',_date) = CURRENT_DATE
         ";
      if (isset($args[4])) {
         $sql .= "AND run_num = '{$args[4]}'";
         }
      $result = pg_query($dbsock,$sql);
      if (pg_result_status($result) != PGSQL_TUPLES_OK) {
         js_err_msg("No tuples returned","sql_today_prod()");
         }
      pg_close($dbsock);
      return pg_fetch_assoc($result);
      }

   function sql_total_cap() {
      $args = func_get_args();
      $dbsock = pg_connect(__DB__) or js_err_msg("Can't connect to database","sql_today_prod()");
      $sql = "
         SELECT avg(capability)
         FROM {$args[3]}
         WHERE model_year = '{$args[0]}'
         AND model_prefix = '{$args[1]}'
         AND model_suffix = '{$args[2]}'
         ";
      if (isset($args[4])) {
          $sql .= "AND run_num = '{$args[4]}'";
          }
      $result = pg_query($dbsock,$sql);
      if (pg_result_status($result) != PGSQL_TUPLES_OK) {
         js_err_msg("No tuples returned","sql_total_cap()");
         }
      pg_close($dbsock);
      return pg_fetch_assoc($result);
      }

   // wkly_cap_chart_google_data (string $table)
   // returns js_string
   function wkly_cap_chart_google_data ($table) {
      $dbsock = pg_connect(__DB__)
         or js_err_msg("Unable to connect to database!","sql_functions.php","wkly_cap_chart_google_data()");
      $js_string = "[['Date','Capability'],";
      $dates = array(
         date("Y-m-d",strtotime("last Monday")),
         date("Y-m-d",strtotime("last Tuesday")),
         date("Y-m-d",strtotime("last Wednesday")),
         date("Y-m-d",strtotime("last Thursday"))
         );
      asort($dates);
      foreach($dates as $day) {
         $sql = "
            SELECT avg(capability)
            FROM {$table}
            WHERE date_trunc('day',_date) = '{$day}'";
         $result = pg_query($dbsock,$sql);
         if (pg_result_status($result) != PGSQL_TUPLES_OK) {
            js_err_msg("Failed to retrieve data!","sql_functions.php","wkly_cap_chart_google_data()");
            }
         $data = pg_fetch_assoc($result);
         $google_data[$day] = round($data['avg'],2);
         }
      foreach($google_data as $x => $y) {
         $js_string .= "['{$x}',{$y}]";
         unset($google_data[$x]);
         if (! empty($google_data)) {
            $js_string .= ",";
            }
         }
      $js_string .= "]";
      pg_close($dbsock);
      return $js_string;
      }

   //sql_today_trk_prod(string $year, string $prefix, string $suffix, string $table [, string $run_num])
   //returns HTML
   function sql_today_trk_prod() {
      $args = func_get_args();
      $dbsock = pg_connect(__DB__)
         or js_err_msg("Unable to connect to database!","sql_functions.php","sql_today_trk_prod()");
      $sql = "
         SELECT id,model_year,model_prefix,model_suffix,number,license,graphic,capability,_date
         FROM trk_prod
         WHERE model_year = '{$args[0]}'
         AND model_prefix = '{$args[1]}'
         AND model_suffix = '{$args[2]}'
         ";
      if (isset($args[3])) {
          $sql .= "AND run_num = '{$args[3]}' ";
          }
      $sql .= "
         AND date_trunc('day',_date) = current_date
         ORDER BY _date DESC";
      $result = pg_query($dbsock,$sql);
      if (pg_result_status($result) != PGSQL_TUPLES_OK) {
         js_err_msg("Failed getting data from server!","sql_functions.php","sql_today_trk_prod()");
         }
      $str ="<table border='1' style='text-align:center;margin-left:auto;margin-right:auto'>
               <tr><th>Date</th><th>Number</th><th>License</th><th>Graphic State</th><th>FTC</th><th>Details</th></tr>";
      $today = pg_fetch_all($result);
      if (! empty($today)) {
         foreach($today as $row) {
            $str .= "<tr>
                  <td>".date('m-d-Y',strtotime($row['_date']))."</td>
                  <td>{$row['model_prefix']} {$row['number']} {$row['model_suffix']}</td>
                  <td>{$row['license']} AZ</td>
                  <td>{$row['graphic']}</td>
                  <td>".round($row['capability'],3)."%</td>
                  <td><button onclick=\"openDetails('truck','{$row['id']}')\">Show Details</button></td></tr>";
               }
            }
        $str .="</table>";
      pg_close($dbsock);
      return $str;
      }

   //sql_total_trk_prod(string $year, string $prefix, string $suffix [, string run_num])
   //returns HTML
   function sql_total_trk_prod() {
      $args = func_get_args();
      $dbsock = pg_connect(__DB__)
         or js_err_msg("Unable to connect to database!","sql_functions.php","sql_total_trk_prod()");
      $sql = "
         SELECT id,model_year,model_prefix,model_suffix,number,license,graphic,capability,_date
         FROM trk_prod
         WHERE model_year = '{$args[0]}'
         AND model_prefix = '{$args[1]}'
         AND model_suffix = '{$args[2]}'
         ";
      if (isset($args[3])) {
          $sql .= "AND run_num = '{$args[3]}' ";
          }
      $sql .= "
         ORDER BY _date DESC";
      $result = pg_query($dbsock,$sql);
      if (pg_result_status($result) != PGSQL_TUPLES_OK) {
         js_err_msg("Failed getting data from server!","sql_functions.php","sql_total_trk_prod()");
         }
      $str ="<table border='1' style='text-align:center;margin-left:auto;margin-right:auto'>
               <tr><th>Date</th><th>Number</th><th>License</th><th>Graphic State</th><th>FTC</th><th>Details</th></tr>";
      foreach(pg_fetch_all($result) as $row) {
         $str .= "<tr>
               <td>".date('m-d-Y',strtotime($row['_date']))."</td>
               <td>{$row['model_prefix']} {$row['number']} {$row['model_suffix']}</td>
               <td>{$row['license']} AZ</td>
               <td>{$row['graphic']}</td>
               <td>".round($row['capability'],3)."%</td>
               <td><button onclick=\"openDetails('truck','{$row['id']}')\">Show Details</button></td></tr>";
            }
        $str .="</table>";
      pg_close($dbsock);
      return $str;
      }

   //sql_today_trlr_prod(string $year, string $prefix, string $suffix [, string $run_num])
   //returns HTML
   function sql_today_trlr_prod() {
      $args = func_get_args();
      $dbsock = pg_connect(__DB__)
         or js_err_msg("Unable to connect to database!","sql_functions.php","sql_today_trlr_prod()");
      $sql = "
         SELECT id,model_year,model_prefix,model_suffix,number,license,license_state,doorgraphic,graphic,capability,_date
         FROM trlr_prod
         WHERE model_year = '{$args[0]}'
         AND model_prefix = '{$args[1]}'
         AND model_suffix = '{$args[2]}'
         ";
      if (isset($args[3])) {
         $sql .= "AND run_num = '{$args[3]}' ";
          }
      $sql .= "
         AND date_trunc('day',_date) = current_date
         ORDER BY _date DESC";
      $result = pg_query($dbsock,$sql);
      if (pg_result_status($result) != PGSQL_TUPLES_OK) {
         js_err_msg("Failed getting data from server!","sql_functions.php","sql_today_trlr_prod()");
         }
      $str ="<table border='1' style='text-align:center;margin-left:auto;margin-right:auto'>
               <tr><th>Date</th><th>Number</th><th>License</th><th>Graphic State</th><th>Door Graphic</th><th>FTC</th><th>Details</th></tr>";
      $today = pg_fetch_all($result);
      if (! empty($today)) {
         foreach($today as $row) {
            $str .= "<tr>
                  <td>".date('m-d-Y',strtotime($row['_date']))."</td>
                  <td>{$row['model_prefix']} {$row['number']} {$row['model_suffix']}</td>
                  <td>{$row['license']} {$row['license_state']}</td>
                  <td>{$row['graphic']}</td>
                  <td>{$row['doorgraphic']}</td>
                  <td>".round($row['capability'],3)."%</td>
                  <td><button onclick=\"openDetails('trailer','{$row['id']}')\">Show Details</button></td></tr>";
               }
            }
        $str .="</table>";
      pg_close($dbsock);
      return $str;
      }

   //sql_today_trlr_prod(string $year, string $prefix, string $suffix [, string $run_num])
   //returns HTML
   function sql_total_trlr_prod($year,$prefix,$suffix) {
      $args = func_get_args();
      $dbsock = pg_connect(__DB__)
         or js_err_msg("Unable to connect to database!","sql_functions.php","sql_total_trlr_prod()");
      $sql = "
         SELECT id,model_year,model_prefix,model_suffix,number,license,license_state,doorgraphic,graphic,capability,_date
         FROM trlr_prod
         WHERE model_year = '{$args[0]}'
         AND model_prefix = '{$args[1]}'
         AND model_suffix = '{$args[2]}'
         ";
      if (isset($args[3])) {
         $sql .= "AND run_num = '{$args[3]}' ";
         }
      $sql .= "
         ORDER BY _date DESC";
      $result = pg_query($dbsock,$sql);
      if (pg_result_status($result) != PGSQL_TUPLES_OK) {
         js_err_msg("Failed getting data from server!","sql_functions.php","sql_total_trlr_prod()");
         }
      $str ="<table border='1' style='text-align:center;margin-left:auto;margin-right:auto'>
               <tr><th>Date</th><th>Number</th><th>License</th><th>Graphic State</th><th>Door Graphic</th><th>FTC</th><th>Details</th></tr>";
      foreach(pg_fetch_all($result) as $row) {
         $str .= "<tr>
               <td>".date('m-d-Y',strtotime($row['_date']))."</td>
               <td>{$row['model_prefix']} {$row['number']} {$row['model_suffix']}</td>
               <td>{$row['license']} {$row['license_state']}</td>
               <td>{$row['graphic']}</td>
               <td>{$row['doorgraphic']}</td>
               <td>".round($row['capability'],3)."%</td>
               <td><button onclick=\"openDetails('trailer','{$row['id']}')\">Show Details</button></td></tr>";
            }
      $str .="</table>";
      pg_close($dbsock);
      return $str;
      }

   function sql_parts_total() {
      $dbsock = pg_connect(__DB__)
         or js_err_msg("Unable to connect to database!","sql_functions.php","sql_parts_total()");
      $sql = 'SELECT count(*) FROM (SELECT DISTINCT part_number FROM parts_lists) as foo';
      $result=pg_query($dbsock,$sql);
      if (pg_result_status($result) != PGSQL_TUPLES_OK) {
         js_err_msg("Call returned bad data!", "sql_functions.php","sql_parts_total()");
         }
      pg_close($dbsock);
      return pg_fetch_result($result,0,0);
      }

   function sql_reporter_id() {
       global $server;
       /*
      $dbsock = pg_connect(__DB__)
         or js_err_msg("Can't connect to database!","sql_functions.php","sql_reporter_id()");
      $sql="SELECT id FROM web_user WHERE session_key ='{$_COOKIE['session_key']}'";
      $result = pg_query($dbsock,$sql);
      if (pg_result_status($result) != PGSQL_TUPLES_OK) {
         js_err_msg("Call returned bad data!","sql_functions.php","sql_reporter_id()");
         }
      pg_close($dbsock);
      return pg_fetch_result($result,0,0);
      */
      return $server->currentUserID;
      }

   function sql_reporter_name($id) {
      $dbsock = pg_connect(__DB__)
         or js_err_msg("Can't connect to database!","sql_functions.php","sql_reporter_name()");
      $sql = "SELECT real_name FROM web_user WHERE id = '{$id}'";
      $result = pg_query($dbsock,$sql);
      if (pg_result_status($result) != PGSQL_TUPLES_OK) {
         js_msg_err("Call returned bad data!","sql_functions.php","sql_reporter_name()");
         }
      pg_close($dbsock);
      return pg_fetch_result($result,0,0);
      }

   function ts_query_string ($search_str) {
      $terms = preg_split(
         "/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[\s,]+/",
         $search_str,
         0,
         PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
         );
      $ts = "to_tsquery('";
      for ($cnt = 0; $cnt < count($terms); $cnt++) {
         if ($terms[$cnt] == "AND" || $terms[$cnt] == "OR") {
            continue;
            }
         else {
            $ts .= "{$terms[$cnt]}";
            }
         if ($terms[$cnt+1] == "AND" && ($cnt+1) != (count($terms)-1)) {
            $ts .= "&";
            continue;
            }
         else if ($terms[$cnt+1] == "OR" && ($cnt+1) != (count($terms)-1)) {
            $ts .= "|";
            continue;
            }
         else if (count($terms) == 1 || $cnt == (count($terms)-1)) {
            break;
            }
         else {
            $ts .= "&";
            }

         }

      $ts .= "')";
      return $ts;
      }

   function auth_area_perms() {
      if (empty($_COOKIE['session_key'])) {
         return "PUBLIC";
         }
      $dbsock = pg_connect(__DB__) or
         js_err_msg("Can't contact database!","sql_functions.php","auth_area_perms()");
      $sql = "SELECT admin,supervisor,ehs,parts,prod,cntr FROM web_user WHERE session_key = '{$_COOKIE['session_key']}'";
      $result = pg_query($dbsock,$sql);
      if (pg_result_status($result) != PGSQL_TUPLES_OK) {
         js_err_msg("No tuples returned!","sql_functions.php","auth_area_perms()");
         }
      pg_close($dbsock);
      return pg_fetch_assoc($result);
      }
/*
   function section_auth() {
      $PERMS = auth_area_perms();
      if ($PERMS == "PUBLIC") {
         return FALSE;
         }
      foreach(func_get_args() as $authorized) {
         if ($PERMS[$authorized] === 't') {
            return TRUE;
            }
         }
      return FALSE;
      }
*/
   function mime_ext($type) {
      //Returns file extension including dot(.) as string.
      $file = fopen("/etc/mime.types","r") or js_err_msg("Can't open file /etc/mime.types!","sql_functions.php","mime_ext()","line 366");
      $mime = fread($file,filesize("/etc/mime.types"));
      fclose($file);
      if (! preg_match("|{$type}\s+([\w]+)|",$mime,$match)) {
         js_err_msg("Mime type not found!","{$type}","mime_ext()","line 368");
         }
      return ".{$match[1]}";
      }

   function last_sunday() {
      //Returns string containing date of last week's Sunday as Y-m-d.
      //Check to see if last Monday was last week or 2 weeks ago.
      if (date('N') == 1) {
         $sun = date('Y-m-d',strtotime("last Monday -1 day"));
         }
      else {
         $sun = date('Y-m-d',strtotime("last Monday -1 week -1 day"));
         }
      return $sun;
      }

   function wx_warn() {
      //Returns array containing strings of weather warning or FALSE.
      global $wx_warnings;
      $xml = fopen(WX_WRN_URL,"rb");
      if (! $xml) {return false;}
      $weather = simplexml_load_string(stream_get_contents($xml)) or false;
      fclose($xml);
      if (!$weather or !$xml) {return false;}
      foreach($weather->xpath("//hazard[@headline]") as $hazard) {
         foreach($wx_warnings as $warning) {
            if ((string)$hazard->attributes() == $warning) {
               if (isset($wx_)) {
                  array_push($wx_, array("url" => (string)$hazard->hazardTextURL, "headline" => (string)$hazard->attributes()));
                  }
               else {
                  $wx_ = array(array("url" => (string)$hazard->hazardTextURL, "headline" => (string)$hazard->attributes()));
                  }
               }
            }
         }
      if (isset($wx_)) {
         return $wx_;
         }
      else {
         return FALSE;
         }
      }

   function _bell() {
      $bell = fopen(__SERIAL,"wb");
      if (! $bell) {return 0;}
      sleep(1);
      fclose($bell);
      return 1;
      }

   function clean_tmp() {
      if (! file_exists(__TEMP__)) {
         if (! mkdir(__TEMP__)) {
            return false;
            }
         }
      else {
         foreach(glob(__TEMP__."/*") as $x) {
            unlink($x);
            }
         return true;
         }
      }

   function post_call_back_ok() {
      $arg = func_get_args();
      echo "<script type='text/javascript'>";
      if (count($arg) >= 1) {
         echo "window.open('{$arg[0]}','_self');";
         }
      else {
         echo "window.open('/etc/dialog.php?dialog=entry_ok','_self');";
         }
      echo "</script>";
      return true;
      }
?>
