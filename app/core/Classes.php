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



class JSONRequest {
    private $requestData = [];

    public function __construct() {
        $this->parseInput();
    }

    private function parseInput() {
        $json = file_get_contents('php://input');
        $this->requestData = json_decode($json, true);
    }

    public function get($key, $default = null) {
        return isset($this->requestData[$key]) ? $this->requestData[$key] : $default;
    }

    public function getAll() {
        return $this->requestData;
    }
}


