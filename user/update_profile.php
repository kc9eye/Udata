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

$server->mustBeValidUser();

$admin = new UserAdministrator($server->pdo);
$admin->setUser($server->security->secureUserID);

if (!empty($_REQUEST)) {
    if ($admin->updateProfile($_REQUEST)) {
        $server->newEndUserDialog('Profile Updated Successfully!',DIALOG_SUCCESS,$server->config['application-root'].'/user/myaccount');
    }
    else {
        $server->newEndUserDialog('Something went wrong with your request, wait a while and try again', DIALOG_FAILURE, $server->config['application-root'].'/user/update_profile');
    }
}

$view = $server->getViewer('Update Profile');
$form = new FormWidgets($view->PageData['wwwroot'].'/scripts');
$form->newForm('My Profile');
$form->hiddenInput(':id',$admin->Profile['id']);
$form->hiddenInput(':uid',$admin->Profile['uid']);
$form->inputCapture(':first','First Name', $admin->Profile['first']);
$form->inputCapture(':middle','Middle/Int.', $admin->Profile['middle']);
$form->inputCapture(':last','Last Name', $admin->Profile['last']);
$form->inputCapture(':other','Jr./Sr./Other', $admin->Profile['other']);
$form->inputCapture(':address','Address', $admin->Profile['address']);
$form->inputCapture(':address_other','Address Cont.',$admin->Profile['address_other']);
$form->inputCapture(':city','City',$admin->Profile['city']);
$form->inputCapture(':state_prov','State/Prov.', $admin->Profile['state_prov']);
$form->inputCapture(':postal_code','Postal/Zip', $admin->Profile['postal_code']);
$form->inputCapture(':home_phone','Home Phone', $admin->Profile['home_phone']);
$form->inputCapture(':cell_phone','Cell Phone', $admin->Profile['cell_phone']);
$form->inputCapture(':alt_phone','Other Phone', $admin->Profile['alt_phone']);
$form->emailCapture(':alt_email','Alt. Email', $admin->Profile['alt_email']);
$form->inputCapture(':e_contact_name','Emergency Contact', $admin->Profile['e_contact_name']);
$form->inputCapture(':e_contact_number','Emergency Phone', $admin->Profile['e_contact_number']);
$form->inputCapture(':e_contact_relation','Emergency Relationship', $admin->Profile['e_contact_relation']);
$form->submitForm('Submit', false, $server->config['application-root']);
$form->endForm();
$view->footer();