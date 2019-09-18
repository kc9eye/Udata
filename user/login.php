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
$view = $server->getViewer('Login');

$failed_login = false;

if (!empty($_POST)) {
    $res = $server->security->verifyLogOn($_POST['username'], $_POST['password']);
    if ($res === true) {
        $_SESSION['uid'] = $server->security->secureUserID;
        if (isset($_POST['remember']) && $_POST['remember'] == 1) {
            $server->security->setPersistentLogOn($server->security->secureUserID);
        }
        if (isset($_SESSION['login-redirect'])) {
            $url = $_SESSION['login-redirect'];
            unset($_SESSION['login-redirect']);
            $server->redirect($url);
        }
        else {
            $server->redirect('');
        }
    }
    else {
        $failed_login = true;
    }
}

?>
<div class='row'>
    <div class='center-text'><h1>Log In</h1></div>
</div>
<?php
    if ($failed_login) {
        echo "<div class='row'>\n
                <div class='col-md-3'></div>\n
                    <div class='col-xs-12 col-md-6'>\n
                        <h4 class='bg-danger text-danger'>
                            Warning: 
                            <small>
                                Incorrect username or password
                                <a href='{$server->config['application-root']}/user/password_reset'>Forgot my password</a>
                            </small>
                        </h4>\n
                    </div>\n
                <div class='col-md-3'></div>\n
            </div>\n";
    }
    elseif (isset($_SESSION['user_privilege_escalation'])) {
        unset($_SESSION['user_privilege_escalation']);
        echo "<div class='row'>
                <div class='col-md-3'></div>
                    <div class='col-xs-12 col-md-6'>
                        <h4 class='bg-danger text-danger'>
                            Warning:
                            <small>
                                The current account does not have sufficient privileges to access this page.
                            </small>
                        </h4>
                    </div>
                <div class='col-md-3'></div>
            </div>\n";
    }
?>
<form method='post' class='form-horizontal'>
    <div class='row'>
        <div class='col-md-3'></div>
        <div class='col-xs-12 col-md-6'>
            <div class='input-group'>
                <span class='input-group-addon'><i class='glyphicon glyphicon-user'></i></span>
                <input id='username' type='email' class='form-control' name='username' placeholder='Email' />
            </div>
        </div>
        <div class='col-md-3'></div>
    </div>
    <div class='row'>
        <div class='col-md-3'></div>
        <div class='col-xs-12 col-md-6'>
            <div class='input-group'>
                <span class='input-group-addon'><i class='glyphicon glyphicon-lock'></i></span>
                <input id='password' type='password' class='form-control' name='password' placeholder='Password' />
            </div>
        </div>
        <div class='col-md-3'></div>
    </div>
    <div class='row'>
        <div class='col-md-3'></div>
        <div class='col-xs-12 col-md-6'>
            <div class='checkbox'>            
                <label><input name='remember' type='checkbox' value='1' id='remember' /> Remember me</label>
            </div>
        </div>
        <div class='col-md-3'></div>
    </div>
    <div class='row'><div class='col-xs-12 col-md-12'></div></div>
    <div class='row'>
        <div class='col-md-3'></div>
        <div class='col-xs-12 col-md-6'>
            <div class='input-group'>
                <button type='submit' class='btn btn-default'>Submit</button>
            </div>
        </div>
        <div class='col-md-3'></div>
    </div>
</form>
<?php
$view->footer();