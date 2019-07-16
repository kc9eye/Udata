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
#This file is temporary, so as to aid in v3 to v4 intgration

#Source the v4 init for access control and 
require_once(dirname(dirname(__DIR__)).'/lib/init.php');
require_once('sql_functions.php');

include(dirname(__DIR__).'/submenu.php');

$server->userMustHaveRole('Production');

define("__DB__","dbname='447001' host=localhost user=udata password=MonkeyFuck34");
