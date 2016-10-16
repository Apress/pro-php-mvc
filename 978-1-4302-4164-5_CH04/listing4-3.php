<?php

function parsePhp($path)
{
    $settings = array();
    include("{$path}.php");
    return $settings;
}

print_r(parsePhp("listing4-1"));