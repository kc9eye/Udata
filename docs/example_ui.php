<?php
/**
 * An example user interface using the UData framework.
 * 
 * This is all that is required to produce a working interface
 * to hold content.
 * @uses init.php
 * @uses Instance
 * @author Paul W. Lane
 */
/**
 * All controllers should begin with sourcing the initialization file.
 * 
 * This gives us access to the global $server variable, to manipulate the Instance.
 * This also, loads the autoloader, errorhandler, security, mailer, and configuration.
 * Any required models needed for the interface can now be called as well.
 */
require_once(dirname(__DIR__).'/lib/init.php');

/**
 * You can use various calls to test for security access before delivering content.
 * 
 * These are all page level security checks that stop script execution in a sane manner,
 * if they are not satisfied.
 */
$server->mustBeValidUser();
$server->userMustHaveRole('User Role');
$server->userMustHavePermission('myPermission');

/**
 * Next we get the viewer object, and in so doing generates the 
 * beginning of the interface.
 */
$view = $server->getViewer('Example Interface');

/**
 * Once the view is started, you can use further security calls, 
 * through out your content, so only specified users see certain
 * content.
 */
if ($server->checkRole('User Role')) {
    $view->h1("Only users with the role 'User Role' see this!");
}
if ($server->checkPermission('myPermission')) {
    $view->h1("Only users with 'myPermission' see this!");
}

if ($server->checkPermission('myOtherPermission')) {
    $view->h1("Only users with 'myOtherPermission' see this!");
}

if ($server->checkPermsArray(['myPermission','myOtherPermission'],true)) {
    $view->h1("Only users with both 'myPermission' and 'myOtherPermission' see this!");
}

/**
 * After which you must close out the interface with a call to the ViewMaker
 */
$view->footer();
