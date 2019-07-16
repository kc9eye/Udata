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
$OK = 0;
$DEFECT = 0;
   $dbsock = pg_connect(__DB__) or js_err_msg("Can't connect to database!","add_truck_func.php","line 23");
//FTC calculation, still extremely simple and robust.
/////////////////////////////////////////////////////
   foreach($_POST as $x) {
      if ($x == 't') {$OK += 1;}
      if ($x == 'f') {$DEFECT += 1;}
      }
   $TOTAL = $OK + $DEFECT;
   $CAP = $OK / $TOTAL * 100;
/////////////////////////////////////////////////////

   $QUERY = "INSERT INTO trk_prod (id,_date,capability";
   foreach($_POST as $x => $y) {
      $QUERY .= ",{$x}";
      }
   $QUERY .= ") VALUES ('".uniqid()."',now(),{$CAP}";
   foreach($_POST as $x) {
      $QUERY .= ",".pg_escape_literal($x);
      }
   $QUERY .= ")";
   /////////////////////DEBUG///////////////////////
   //echo "<pre>",$QUERY,"</pre>";
   //die();
   /////////////////////////////////////////////////
   $RESULT = pg_query($dbsock,$QUERY);
   if (pg_result_status($RESULT) != PGSQL_COMMAND_OK) {
      $server->newEndUserDialog("Something went wrong with the request.",DIALOG_FAILURE,$server->config['application-root'].'/products/main');
      }
   else {
      $server->newEndUserDialog("Legacy Entry Added.",DIALOG_SUCCESS,$server->config['application-root'].'/products/main');
      }

