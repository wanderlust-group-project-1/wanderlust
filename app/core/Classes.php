<?php
class JSONResponse {
    private $response = [];
    private $statusCode = 200;

    public function success($success = true) {
        $this->response['success'] = $success;
        return $this;
    }

    public function data($data = null) {
        $this->response['data'] = $data;
        return $this;
    }

    public function message($message = '') {
        $this->response['message'] = $message;
        return $this;
    }

    public function statusCode($code) {
        $this->statusCode = $code;
        return $this;
    }

    public function send() {
        http_response_code($this->statusCode);
        header('Content-Type: application/json');
        echo json_encode($this->response);
        exit;
    }
}



// class JSONRequest {
//     private $requestData = [];

//     public function __construct() {
//         $this->parseInput();
//     }

//     private function parseInput() {
//         $json = file_get_contents('php://input');
//         $this->requestData = json_decode($json, true);
//     }

//     public function get($key, $default = null) {
//         return isset($this->requestData[$key]) ? $this->requestData[$key] : $default;
//     }

//     public function getAll() {
//         return $this->requestData;
//     }
// }


class JSONRequest {
    private $requestData = [];

    // Default constructor to parse JSON input
    public function __construct() {
        $this->parseInput();
    }

    // Separate constructor for FormData input
    public static function createFromFormData() {
        $instance = new self();
        $instance->parseFormData();
        return $instance;
    }

    private function parseInput() {
        $json = file_get_contents('php://input');
        $this->requestData = json_decode($json, true);
    }

    private function parseFormData() {
        $this->requestData = $_POST; // Assuming form data is sent as POST fields
        $jsonField = $this->get('json');

        if ($jsonField !== null) {
            // If a 'json' field exists in FormData, merge its contents into the request data
            $jsonArray = json_decode($jsonField, true);

            if (is_array($jsonArray)) {
                $this->requestData = array_merge($this->requestData, $jsonArray);
            }
        }
    }

    public function get($key, $default = null) {
        return isset($this->requestData[$key]) ? $this->requestData[$key] : $default;
    }

    public function getAll() {
        return $this->requestData;
    }
    
    // Additional method to get the JSON field from FormData
    public function getJSONFromFormData() {
        return $this->get('json');
    }
}


require '../app/core/Classes/Email.php';
require '../app/core/Classes/QueryBuilder.php';