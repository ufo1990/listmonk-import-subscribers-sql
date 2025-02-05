<?php
/*
API Create subscribers
*/

// Configuration
require('assets/config.php');

// Classes
require_once('classes/curl.php');
require_once('classes/database.php');

// Instances
$curl = new CurlRequest();
$database = new Database($config);

// CURL Authorization
$curl->setAuth($config['api_user'], $config['api_password']);

// SQL query
$stmt = $database->connect()->prepare("SELECT * FROM users");
$stmt->execute();

// Loop to receive data from SQL
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

	// Mapping data from SQL to API
	$data = [
		'email'		=> $row['email'],
		'name'		=> $row['name'],
		'status'	=> 'enabled',
		'attribs' 	=> ["UID" => $row['UID'], 
						"isEmployee" => $row['isEmployee'],
						"isStudent" => $row['isStudent'],
						"isAlumni" => $row['isAlumni']
		]
	];
	
	// POST request
    $postResponse = $curl->post($config['api_host'] . "/api/subscribers", $data);
	
	// Log any errors
    if (!empty($postResponse['error'])) {
        error_log("Unable create subscribers - Error: " . $postResponse['error']);
    } 
}