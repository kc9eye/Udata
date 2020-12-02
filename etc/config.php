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
/**
 * Main configuration file
 * 
 * This file should contain only the `$config` variable. Which contains an indexed array
 * of configuration options for the framework.
 * @package UData
 * @author Paul W. Lane
 * @var Array $config Contains all the application configuration variablse
 */
$config = [
    #Database configuration options
    'dbuser' => '',                                                      #Database user for the active db
    'dbpass' => 'l',                                               #Database users password for the active db
    'dbpdo' => '',  #PDO database connection string 'dbdriver:host=mydbhost;port=mydbport;dbnam=mydbname;[sslmode=mydbencryption...]

    #Errorhandler config options
    'error-log-file-path' => '',    #Where to save th error log file, hard disk location (URL not supported at this time)
    'error-support-link' => 'https://github.com/kc9eye/UData/issues', #A link to bug/issue reporting infrastructure, users will see this.

    #PHPMailer configuration
    'mailer-type' => 'SMTP',            #The mailer to use, SMTP (sends as SMTP), Mail (sends with PHP mail function), Sendmail (uses host sendmail server)
    'mailer-default-from-addr' => 'noreply@udata.com',   #The address the server will send mail as if not defined at send time
    'mailer-default-from-name' => 'UData Server',   #The name the server will send mail as if not defined at send time
    'mailer-host' => '',                #comma separated list is acceptable in order of percedence
    'mailer-SMTPAuth' => true,         #If this is set to true then the security type should also be set
    'mailer-username' => '',            #Required if above is true
    'mailer-password' => '',            #Same as above
    'mailer-security' => '',            #Either 'tls' or 'ssl'
    'mailer-port' => '',                #The SMTP server port to connect to 
    'mailer-custom-opts' => [],         #Custom SMTP options you may need to set for the mailer as an array

    #Application settings dependant on server settings
    'application-root' => '',         #The applications URL (Depends on how to machine is accessed what this should be.)
    'data-root' => '',                  #Where data files are going to be stored. SDS files, images, etc... Must be writtable by the server process
    'template-root' => '',     #Where the application templates are stored for file_get_contents
    
    #ViewMaker pagedata settings, effect how the UI looks
    'company-name' => 'UData',
    'company-motto' => 'The next big thing!',
    'home-name' => 'Safety',
    'theme' => 'dark', #Not implemented as yet

    #Main navbar links
    'navbar-links' => [
        'Products' => '/products/main',
        'Work Cells' =>'/cells/main',
        'Material' => '/material/main',
        'Maintenance' => '/maintenance/main',
        'Human Resources' => '/hr/main',
    ],

    #Background operations
    'services-run' => 'onaccess'        #When background serveices shoudl run, options are 'onacess' (services are run on every page acess), 'cron' (services are run by OS as a cron job). 
];
