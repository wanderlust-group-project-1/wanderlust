<?php

function show($stuff): void {
    echo "<pre>";
    print_r($stuff);
    echo "</pre>";
}


function esc(string $str): string {
    return htmlspecialchars($str);
}

function redirect(string $path): void {
    header("Location: " . ROOT_DIR . "/" . $path);
}

// file upload function , pass file data as parameter

function upload($file,$path): string {
    $target_dir = "./uploads/".$path."/";
    $filename = uniqid();
    // uuid.extention
    $target_file = $target_dir . $filename . "." . pathinfo($file["name"], PATHINFO_EXTENSION);
    // $target_file = $target_dir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    // show($target_file);
// only images or pdf
    // if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    //     && $imageFileType != "gif" && $imageFileType != "pdf") {
    //     $uploadOk = 0;
    // }
    
    // // Check file size
    // if ($file["size"] > 500000) {
    //     $uploadOk = 0;
    // }







//     $directory = "uploads/"; // Specify the directory path
// $files = scandir($directory); // Get the list of files in the directory

// // Output the list of files
// echo "<h2>Files in the directory:</h2>";
// echo "<ul>";
// foreach($files as $file) {
//     if($file != "." && $file != "..") {
//         echo "<li>$file</li>";
//     }
// }
// echo "</ul>";





   
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {

        return "";
    } else {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            // return basename($file["name"]);
            return $filename . "." . pathinfo($file["name"], PATHINFO_EXTENSION);
        } else {
            return "";
        }
    }

    
}
