<?php

include("_base.php");
include("_exceptions.php");
include("_inspector.php");
include("_methods.php");
include("configuration.php");

$configuration = new Framework\Configuration(array(
    "type" => "ini"
));

$configuration = $configuration->initialize();
$parsed = $configuration->parse("_configuration");

print_r($parsed);