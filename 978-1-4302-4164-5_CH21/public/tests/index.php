<?php

define("APP_PATH", dirname(dirname(dirname(__FILE__))));

// initialize core

require("../../framework/core.php");
Framework\Core::initialize();

// load different test sets

require("_cache.php");
require("_configuration.php");
require("_database.php");
require("_model.php");
require("_template.php");
require("_forms.php");
require("_functions.php");

// connect to database

$database = new Framework\Database(array(
    "type" => "mysql",
    "options" => array(
        "host" => "localhost",
        "username" => "prophpmvc",
        "password" => "prophpmvc",
        "schema" => "prophpmvc"
    )
));
$database = $database->initialize();
$database = $database->connect();

// execute tests

$results = Framework\Test::run(
    // setup
    function() use ($database){
        // do nothing
    },
    // cleanup
    function() use ($database){
        $database->execute("DROP TABLE `example`");
        $database->execute("DELETE FROM `user` WHERE `email` = \"info@example.com\" AND `password` = \"password\"");
    }
);

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Unit Tests</title>
        <style type="text/css">
            body
            {
                padding: 25px;
                margin: 0;
                font-family: "Helvetica";
                font-size: 12px;
                color: #333;
            }
            .results span
            {
                font-size: 30px;
                font-family: "Arial Narrow";
                font-weight: bold;
                margin-right: 30px;
            }
            .results .passed
            {
                color: green;
            }
            .results .failed, .results .exceptions
            {
                color: red;
            }
            .exceptions > .title, .failed > .title, .passed > .title
            {
                font-size: 26px;
                font-family: "Arial Narrow";
                font-weight: bold;
                margin-top: 20px;
            }
            .exceptions .test, .failed .test, .passed .test
            {
                border-top: 1px solid #f0f0f0;
                padding: 10px;
            }
            .exceptions .test:nth-child(even), .failed .test:nth-child(even), .passed .test:nth-child(even)
            {
                background: #f5f5f5;
            }
            .test > .set
            {
                font-weight: bold;
                margin-right: 20px;
            }
            .test > .exception
            {
                font-weight: bold;
                margin-left: 20px;
            }
        </style>
    </head>
    <body>
        <div class="results">
            <span class="passed">Passed: <?php echo sizeof($results["passed"]); ?></span>
            <span class="failed">Failed: <?php echo sizeof($results["failed"]); ?></span>
            <span class="exceptions">Exceptions: <?php echo sizeof($results["exceptions"]); ?></span>
        </div>
        <?php if (sizeof($results["exceptions"])): ?>
            <div class="exceptions">
                <div class="title">Exceptions</div>
                <?php foreach ($results["exceptions"] as $exception): ?>
                    <div class="test"><span class="set"><?php echo $exception["set"]; ?></span> <span class="title"><?php echo $exception["title"]; ?></span> <span class="exception"><?php echo $exception["type"]; ?></span></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (sizeof($results["failed"])): ?>
            <div class="failed">
                <div class="title">Failed</div>
                <?php foreach ($results["failed"] as $fail): ?>
                    <div class="test"><span class="set"><?php echo $fail["set"]; ?></span> <span class="title"><?php echo $fail["title"]; ?></span></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (sizeof($results["passed"])): ?>
            <div class="passed">
                <div class="title">Passed</div>
                <?php foreach ($results["passed"] as $pass): ?>
                    <div class="test"><span class="set"><?php echo $pass["set"]; ?></span> <span class="title"><?php echo $pass["title"]; ?></span></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </body>
</html>