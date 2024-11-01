<?php
/*
Plugin Name: WP Google PageSpeed Image Optimizer Lite
Plugin URI: http://www.ps-omni.nl/
Description: WP Google PageSpeed Image Optimizer made by <a href="http://codecanyon.net/user/R3dRidl3" title="R3dRidl3">R3dRidl3</a>
Author: R3dRidl3
Version: 1.3.4
Author URI: http://codecanyon.net/user/R3dRidl3
*/



$upload_dir = wp_upload_dir();
define('WPGIO_UPLOAD_PATH', $upload_dir['path']);
define('WPGIO_UPLOAD_SUBDIR', $upload_dir['subdir']);
define('WPGIO_UPLOAD_BASEDIR', $upload_dir['basedir']);
if(strstr($upload_dir['url'],'http'))
{
	define('WPGIO_UPLOAD_URL', $upload_dir['url']);
}
else
{
	define('WPGIO_UPLOAD_URL', get_site_url() . $upload_dir['url']);
}
if(strstr($upload_dir['url'],'http'))
{
	define('WPGIO_UPLOAD_BASEURL', $upload_dir['baseurl']);
}
else
{
	define('WPGIO_UPLOAD_BASEURL', get_site_url() . $upload_dir['baseurl']);
}

define('WPGIO_GOOGLE_OPTIMIZE_URL', 'https://www.googleapis.com/pagespeedonline/v3beta1/optimizeContents?key='.get_option('google_image_optimizer_api_key').'&url=');
define('WPGIO_GOOGLE_STRATEGY', '&strategy=desktop');

require_once('google-image-optimizer-helpers.php');
require_once('google-image-optimizer-functions.php');
require_once("google-image-optimizer-filters.php");
require_once('google-image-optimizer-actions.php');
require_once('google-image-optimizer-admin-view.php');
require_once('google-image-optimizer-admin-styles.php');
require_once('google-image-optimizer-admin-scripts.php');

$GoogleImageOptimizer = new GoogleImageOptimizerFilters();
$GoogleImageOptimizer->google_image_optimizer_do_filters();

$GoogleImageOptimizer = new GoogleImageOptimizerActions();
$GoogleImageOptimizer->google_image_optimizer_do_actions();