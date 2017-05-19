<?php
// include the API
require('cloudfiles/cloudfiles.php');

// cloud info
$username = "bestal@sctechgroup.com"; // username
$key = "74157bec833047cc8b14541d7fe2103f"; // api key

// Connect to Rackspace
$auth = new CF_Authentication($username, $key);
$auth->authenticate();
$conn = new CF_Connection($auth);

// Get the container we want to use
//~ $container = $conn->create_container('EloAutoTest');
$container = $conn->get_container('EloAutoTest');

// store file information
$localfile = $_FILES['upload']['tmp_name'];
$filename = 'wp-content/'.$_FILES['upload']['name'];

// upload file to Rackspace
$object = $container->create_object($filename);
$object->load_from_filename($localfile);
?>