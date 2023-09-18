<?php

function show($stuff){
    echo "<pre>";
    print_r($stuff);
    echo "</pre>";

}


function esc($str)
{
    return htmlspecialchars($str);
}

function redirect($path)
{
    header("Location:".ROOT_DIR."/".$path);
}