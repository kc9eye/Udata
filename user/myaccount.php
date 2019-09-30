<?php
/* This file is part of UData.
 * Copyright (C) 2019 Paul W. Lane <kc9eye@outlook.com>
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

if (!empty($_REQUEST['action'])) {
    switch($_REQUEST['action']) {
        case 'updatetheme':
            $handler = new UserServices($server);
            $server->processingDialog(
                [$handler,'updateAccountTheme'],
                [$_REQUEST['theme'],$_REQUEST['pid']],
                $server->config['application-root'].'/user/myaccount'
            );
        break;
        default: main(); break;
    }
}
else {
    main();
}

function main () {
    global $server;
    $user = new User($server->pdo,$server->currentUserID);
    $view = $server->getViewer('My Account');
    $form = new InlineFormWidgets($view->PageData['wwwroot'].'/scripts');
    $view->h1('<small>UData Account Settings for:</small>'.$user->getFullName());
    $view->hr();
    $view->h3('Account Actions');
    $view->linkButton('/user/password_reset?username='.$user->getUserName(),'Reset Password','warning');
    $view->insertTab();
    $view->linkButton('/user/myaccount?action=delete','DELETE Account','danger');
    $view->hr();
    $view->h3('Account Settings');
    $view->bold('My Notifications');
    $view->responsiveTableStart();
    foreach($user->getUserNotifications() as $row) {
        echo "<tr><td>{$row['description']}</td></tr>\n";
    }
    $view->responsiveTableClose();

    $form->newInlineForm();
    $form->hiddenInput('action','updatetheme');
    $form->hiddenInput('pid',$user->pid);
    $form->inlineSelectBox(
        'theme',
        'Theme',
        [
            ['','Dark'],
            ['light-theme.js','Light'],
            ['uhaul-theme.js','Uhaul'],
            ['bears-theme.js','Chicago Bears'],
            ['cubs-theme.js','Chicago Cubs']
        ]
    );
    $form->inlineSubmit();
    $form->endInlineForm();
    $view->hr();
    $view->h3('Account Profile');
    $profile = $user->getProfileArray();
    $form->newForm();
    $form->hiddenInput('action','updateprofile');
    $form->inputCapture('first','First Name',$profile['first']);
    $form->inputCapture('middle','Middle Name',$profile['middle']);
    $form->inputCapture('last','Last Name',$profile['last']);
    $form->inputCapture('address','Address',$profile['address']);
    $form->inputCapture('address_other','Address Cont.',$profile['address_other']);
    $form->inputCapture('city','City',$profile['city']);
    $form->inputCapture('state_prov','State/Prov.',$profile['state_prov']);
    $form->inputCapture('postal_code','Postal Code',$profile['postal_code']);
    $form->inputCapture('home_phone','Home Phone',$profile['home_phone']);
    $form->inputCapture('cell_phone','Cell Phone',$profile['cell_phone']);
    $form->inputCapture('alt_phone','Other Phone',$profile['alt_phone']);
    $form->inputCapture('e_contact_name','Emergency Contact',$profile['e_contact_name']);
    $form->inputCapture('e_contact_relation','Contact Relation',$profile['e_contact_relation']);
    $form->inputCapture('e_contact_number','Emergency Number',$profile['e_contact_number']);
    $form->submitForm('Update');
    $form->endForm();
    $view->footer();
}