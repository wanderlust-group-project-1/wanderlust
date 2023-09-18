<?php

trait Controller {
    public function view(string $name, array $data = []): void {
        if (!empty($data)) {
            extract($data);
        }
        
        $filename = "../app/views/" . $name . ".view.php";
        
        if (file_exists($filename)) {
            require $filename;
        } else {
            require "../app/views/404.view.php";
        }
    }
}
