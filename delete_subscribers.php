<?php
/*
API Update subscribers
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
	/*
    * Get subscribers IDs
    */
	
	// Build the full URL with parameters
    $urlParameters = http_build_query(['query' => "(subscribers.attribs->>'UID')::INT={$row['UID']}"]);
	$getResponse = $curl->get($config['api_host'] . '/api/subscribers?' . $urlParameters);

	// Validate response
    if (empty($getResponse['response']['data']['results'][0]['id'])) {
        error_log("Subscriber ID not found for UID: " . $row['UID']);
        continue; // Skip to the next user
    }
	
	// Assign subscribers IDs to variable
	$subscriberIds = $getResponse['response']['data']['results'][0]['id'];
	
	/*
    * Delete subscribers data, base on subscribers IDs
    */
	
	// DELETE request
    $deleteResponse = $curl->delete($config['api_host'] . "/api/subscribers/" . $subscriberIds);
	
	// Log any errors
    if (!empty($deleteResponse['error'])) {
        error_log("Delete failed for UID: " . $row['UID'] . " - Error: " . $deleteResponse['error']);
    }   
}