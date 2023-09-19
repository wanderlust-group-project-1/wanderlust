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

// function cookie set 

