<?php
/*
Plugin Name: Cloud Files Rackspace CDN
Text Domain: cdn-cloudfiles-rackspace
Description: Add Rackspace CDN Integration to your website.
Author: Evan Garcia
Author URI: https://sctechgroup.com
License: GPLv2 or later
Version: 1.0.0
*/

 //~ if(!file_exists("../wp-content/uploads/2017/05/handyman_services.jpg")){
 //~ echo dirname(__FILE__)."/../wp-content/uploads/2017/05/handyman_services.jpg"."<br />";
 //~ die("not found @ ".dirname(__FILE__));
 //~ }else{
  //~ die("FOUND");
 
 //~ }


/* Check & Quit */
defined('ABSPATH') OR exit;


/* constants */
define('RACKSPACE_CDN_FILE', __FILE__);
define('RACKSPACE_CDN_DIR', dirname(__FILE__));
define('RACKSPACE_CDN_BASE', plugin_basename(__FILE__));
define('RACKSPACE_CDN_MIN_WP', '3.8');


function UploadToCDN($post_ID) {
    $src = wp_get_attachment_image_src( $post_ID, 'large' )[0];
    //~ $metadata=wp_get_attachment_metadata($post_ID);
   //~ $filepath=$metadata['file'];
   $filepath=ltrim(str_replace(get_option('home'),'',$src),'/');
   $filename=explode("/",$filepath);
   $filename=end($filename);
    //~ die($src);
    //~ $lum = get_avg_luminance($src, 10, true);
    //~ add_post_meta( $post_ID, 'image_lum', $lum, true ) || update_post_meta( $post_ID, 'image_lum', $lum );
    if(file_exists("../".$filepath)){
    
    
    //~ $postTitle=get_the_title($post_ID);
    //~ $my_postData = array(
      //~ 'ID'           => $post_ID,
     // 'post_title'   => $filepath.': '.$postTitle
      //~ 'post_title'   => $filepath.': '.$postTitle
  //~ );
    //~ wp_update_post($my_postData);
    
    
    //~ $metadata['image_meta']['title']="OK: ".$metadata['image_meta']['title'];
    //~ wp_update_attachment_metadata( $post_ID,  $metadata );
   //~ // include the API
   ob_start();
   //~ ob_end_clean();
require_once('inc/rackspace_cloud_upload/cloudfiles/cloudfiles.php');


$pluginOptions=wp_parse_args(
			get_option('RACKSPACE_CDN'),
			array(
                'url' => get_option('home'),
                'username' => '',
                'apikey' => '',
                'container' => '',
                'dirs' => 'wp-content/uploads',
                'excludes' => '.php',
                'localurls' => 1,
                'https' => 1
			)
		);

//~ // cloud info
//$username = "bestal@sctechgroup.com"; // username
$username = $pluginOptions['username']; // username
$key = $pluginOptions['apikey']; // api key

// Connect to Rackspace
$auth = new CF_Authentication($username, $key);
$auth->authenticate();
$conn = new CF_Connection($auth);

//~ // Get the container we want to use
//$container = $conn->create_container('EloAutoTest');
//$container = $conn->get_container('EloAutoTest');
$container = $conn->get_container($pluginOptions['container']);


// upload file to Rackspace
$object = $container->create_object($filepath);
$object->load_from_filename("../".$filepath); 
$filecdnUrl = $object->getPublicUrl();
$out1 = ob_get_contents();
//~ $out1 = "";
ob_end_clean();
//~ throw new Exception('myerror: '.$out1);
         
    $my_postData = array(
      'ID'           => $post_ID,
      'post_title'   => 'uploaded": '.$filecdnUrl
      //~ 'post_title'   => 'ok:'.$out1
  );
    wp_update_post($my_postData); 
    
    
    }else{
    //~ die("file not found");
    $my_postData = array(
      'ID'           => $post_ID,
      'post_title'   => 'file not found": '.$filepath
  );
    wp_update_post($my_postData);
    
    }
    
    return $post_ID;
}

add_filter('add_attachment', 'UploadToCDN', 10, 2);

/* loader */
add_action(
	'plugins_loaded',
	array(
		'RACKSPACE_CDN',
		'instance'
	)
);


/* uninstall */
register_uninstall_hook(
	__FILE__,
	array(
		'RACKSPACE_CDN',
		'handle_uninstall_hook'
	)
);


/* activation */
register_activation_hook(
	__FILE__,
	array(
		'RACKSPACE_CDN',
		'handle_activation_hook'
	)
);


/* autoload init */
spl_autoload_register('RACKSPACE_CDN_autoload');

/* autoload funktion */
function RACKSPACE_CDN_autoload($class) {
	if ( in_array($class, array('RACKSPACE_CDN', 'RACKSPACE_CDN_REWRITER', 'RACKSPACE_CDN_SETTINGS')) ) {
		require_once(
			sprintf(
				'%s/inc/%s.class.php',
				RACKSPACE_CDN_DIR,
				$class
			)
		);
	}
}
