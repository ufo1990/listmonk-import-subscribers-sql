<?php
/*
CurlRequest
*/

class CurlRequest {
    // Request URL
    private $url;
    
    // Array of HTTP headers
    private $headers = [];
    
    // Authentication credentials (username:password)
    private $auth = null;
    
    // Stores the response from the request
    private $response;
    
    // Stores any cURL error messages
    private $error;
    
    // Stores the HTTP status code of the response
    private $httpCode;
	
	// Constructor - Initializes the request URL
    public function __construct($url = null) {
        $this->url = $url;
    }

    // Sets a new request URL
    public function setUrl($url) {
        $this->url = $url;
    }

    // Sets custom HTTP headers for the request
    public function setHeaders(array $headers) {
        $this->headers = $headers;
    }

    // Sets authentication credentials for the request
    public function setAuth($username, $password) {
        $this->auth = "$username:$password";
    }

    // Sends an HTTP request using cURL
    private function sendRequest($method, $url = null, $data = null) {
        if (!$url) {
            $url = $this->url; // Use default if no URL provided
        }

        if (!$url) {
            throw new Exception("URL is not set for CurlRequest.");
        }

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => array_merge($this->headers, ['Content-Type: application/json; charset=utf-8']),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);

        if ($this->auth) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->auth);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $this->response = curl_exec($ch);
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->error = curl_error($ch);
        curl_close($ch);
    }

    // Sends a GET request
    public function get($url = null) {
        $this->sendRequest('GET', $url);
        return $this->getResponse();
    }

    // Sends a POST request
    public function post($url, $data) {
        $this->sendRequest('POST', $url, $data);
        return $this->getResponse();
    }

    // Sends a PUT request
    public function put($url, $data) {
        $this->sendRequest('PUT', $url, $data);
        return $this->getResponse();
    }

    // Sends a DELETE request
    public function delete($url = null) {
        $this->sendRequest('DELETE', $url);
        return $this->getResponse();
    }

    // Retrieves the response data from the last request
    private function getResponse() {
        return [
            'http_code' => $this->httpCode,
            'response' => json_decode($this->response, true),
            'error' => $this->error ?: null
        ];
    }
}