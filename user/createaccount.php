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
if (!empty($_POST)) {
    $account = new AccountCreator($server->pdo, $server->mailer, $server->config['application-root']);
    if ($account->checkUsername($_POST['email'])) {
        $server->newEndUserDialog('That username already exists, please choose another one.',DIALOG_FAILURE,$server->config['application-root'].'/user/createaccount');
    }
    if (($res = $account->createAccountToVerify($_POST)) !== true) {
        switch ($res) {
            case $account::AWAITING_VERIFICATION : 
                $server->newEndUserDialog("That username is already registered and awaiting verification, check your email for the verification.");
            break;
            default:
                $server->newEndUserDialog("Something went wrong with the request.");
            break;
        }
        $server->newEndUserDialog('Something went wrong with the request. Try again later.',DIALOG_FAILURE,$server->config['application-root']);
    }
    else {
        $server->newEndUserDialog('An email was sent to the provided address for verification.',DIALOG_SUCCESS);
    }
}
$viewer = $server->getViewer('Create Account');
?>
<div style='margin:10px'>
    <div class='center-text center-block'><h1>Create Account</h1></div>
    <form method='post' class='form-horizontal' id='createaccount'>
        <div class='form-group'>
            <div class='row'>
                <div class='col-md-3'><!-- This is for spacing on desktop --></div>
                <div class='col-xs-12 col-md-1 right-text'>
                    <label class='control-label' for='email'>Email:</label>
                </div>
                <div class='col-xs-12 col-md-5'>
                    <input type='email' class='form-control' id='email' placeholder='Enter Email' name='email' />
                    <span class='help-block'>
                        You will receive a confirmation to this email and it will also serve as your username.
                        Make sure it is valid.
                    </span>
                </div>
                <div class='col-md-3'><!-- This is for spacing on desktop --></div>
            </div>
        </div>
        <div class='form-group'>
            <div class='row'>
                <div class='col-md-3'><!-- Desktop spacing --></div>
                <div class='col-xs-12 col-md-1 right-text'>
                    <label class='control-label' for='password'>Password:</label>
                </div>
                <div class='col-xs-12 col-md-5'>
                    <input type='password' class='form-control' id='password' name='password' placeholder='Password' />
                </div>
                <div class='col-md-3'><!-- Desktop spacing --></div>
            </div>
        </div>
        <div class='form-group'>
            <div class='row'>
                <div class='col-md-3'><!-- Desktop spacing --></div>
                <div class='col-xs-12 col-md-1 right-text'>
                    <label class='control-label' for='verify'>Verify:</label>
                </div>
                <div class='col-xs-12 col-md-5'>
                    <input type='password' class='form-control' id='verify' name='verify' placeholder='Verify Password' />
                </div>
                <div class='col-md-3'><!-- Desktop sizing --></div>
            </div>
        </div>
        <div class='form-group'>
            <div class='row'>
                <div class='col-md-3'></div>
                <div class='col-xs-12 col-md-1 right-text'>
                    <label class='control-label' for='firstname'>Firstname:</label>
                </div>
                <div class='col-xs-12 col-md-5'>
                    <input type='text' class='form-control' id='firstname' name='firstname' placeholder='Firstname Required' />
                </div>
                <div class='col-md-3'></div>
            </div>
        </div>
        <div class='form-group'>
            <div class='row'>
                <div class='col-md-3'></div>
                <div class='col-xs-12 col-md-1 right-text'>
                    <label class='control-label' for='lastname'>Lastname:</label>
                </div>
                <div class='col-xs-12 col-md-5'>
                    <input type='text' class='form-control' id='lastname' name='lastname' placeholder='Optional' />
                </div>
                <div class='col-md-3'></div>
            </div>
        </div>

        <div class='form-group'>
            <div class='row'>
                <div class='col-md-3'></div>
                <div class='col-xs-12 col-md-1 right-text'>
                    <label class='control-label' for='altemail'>Alt. Email:</label>
                </div>
                <div class='col-xs-12 col-md-5'>
                    <input type='email' class='form-control' id='altemail' name='altemail' placeholder='Optional' />
                </div>
                <div class='col-md-3'></div>
            </div>
        </div>
        <div class='form-group'>
            <div class='row'>
                <div class='col-md-4'><!-- desktop spacing --></div>
                <div class='col-xs-12 col-md-6'>
                    <button class='btn btn-lg btn-default' type='submit'>Create Account</button>
                </div>
                <div class='col-md-2'><!-- desktop spacing --></div>
            </div>
        </div>
    </form>
</div>
<?php
$scripts = [
    'https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js',
    'https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/additional-methods.min.js',
    $viewer->PageData['wwwroot'].'/scripts/createaccount.js'
];
$viewer->footer($scripts);