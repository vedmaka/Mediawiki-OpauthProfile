<?php
/**
 * Initialization file for the OpauthProfile extension.
 *
 * @file OpauthProfile.php
 * @ingroup OpauthProfile
 *
 * @licence GNU GPL v3
 * @author Wikivote llc < http://wikivote.ru >
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

if ( version_compare( $wgVersion, '1.17', '<' ) ) {
	die( '<b>Error:</b> This version of OpauthProfile requires MediaWiki 1.17 or above.' );
}

global $wgOpauthProfile;
$wgOpauthProfileDir = dirname( __FILE__ );

/* Credits page */
$wgExtensionCredits['specialpage'][] = array(
    'path' => __FILE__,
    'name' => 'OpauthProfile',
    'version' => '0.1',
    'author' => 'Jon Anderton',
    'url' => '',
    'descriptionmsg' => 'OpauthProfile-credits',
);

/* Resource modules */
$wgResourceModules['ext.OpauthProfile.main'] = array(
    'localBasePath' => dirname( __FILE__ ) . '/modules/',
    'remoteExtPath' => 'OpauthProfile/',
    'group' => 'ext.OpauthProfile',
    'scripts' => array(),
    'styles' => array(
    	'css/style.css'
    ),
	'position' => 'top'
);

/* Message Files */
$wgExtensionMessagesFiles['OpauthProfile'] = dirname( __FILE__ ) . '/OpauthProfile.i18n.php';

/* Autoload classes */
$wgAutoloadClasses['OpauthProfile'] = dirname( __FILE__ ) . '/OpauthProfile.class.php';
$wgAutoloadClasses['OpauthProfileHooks'] = dirname( __FILE__ ) . '/OpauthProfile.hooks.php';
$wgAutoloadClasses['SpecialUserProfile'] = dirname( __FILE__ ) . '/specials/SpecialUserProfile.php';

/* ORM,MODELS */
#$wgAutoloadClasses['OpauthProfile_Model_'] = dirname( __FILE__ ) . '/includes/OpauthProfile_Model_.php';

/* ORM,PAGES */
#$wgAutoloadClasses['OpauthProfileSpecial'] = dirname( __FILE__ ) . '/pages/OpauthProfileSpecial/OpauthProfileSpecial.php';

/* Rights */
#$wgAvailableRights[] = 'example_rights';

/* Permissions */
#$wgGroupPermissions['sysop']['example_rights'] = true;

/* Special Pages */
$wgSpecialPages['UserProfile'] = 'SpecialUserProfile';

/* Hooks */
$wgHooks['OpauthLoginUserCreated'][] = 'OpauthProfileHooks::onOpauthLoginUserCreated';
$wgHooks['LoadExtensionSchemaUpdates'][] = 'OpauthProfileHooks::onLoadExtensionSchemaUpdates';
$wgHooks['PostLoginRedirect'][] = 'OpauthProfileHooks::onPostLoginRedirect';
$wgHooks['OpauthLoginFinalRedirect'][] = 'OpauthProfileHooks::onOpauthLoginFinalRedirect';
$wgHooks['AddNewAccount'][] = 'OpauthProfileHooks::onAddNewAccount';