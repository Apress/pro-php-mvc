<?php

Framework\Test::add(
    function()
    {
        $configuration = new Framework\Configuration();
        return ($configuration instanceof Framework\Configuration);
    },
    "Configuration instantiates in uninitialized state",
    "Configuration"
);

Framework\Test::add(
    function()
    {
        $configuration = new Framework\Configuration(array(
            "type" => "ini"
        ));
        
        $configuration = $configuration->initialize();
        return ($configuration instanceof Framework\Configuration\Driver\Ini);
    },
    "Configuration\Driver\Ini initializes",
    "Configuration\Driver\Ini"
);

Framework\Test::add(
    function()
    {
        $configuration = new Framework\Configuration(array(
            "type" => "ini"
        ));
        
        $configuration = $configuration->initialize();
        $parsed = $configuration->parse("_configuration");
        
        return ($parsed->config->first == "hello" && $parsed->config->second->second == "bar");
    },
    "Configuration\Driver\Ini parses configuration files",
    "Configuration\Driver\Ini"
);
