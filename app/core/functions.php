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