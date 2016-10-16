<?php

/*
 *    Assuming the following database tables:
 *
 *    CREATE TABLE `users` (
 *        `id` int(11) NOT NULL AUTO_INCREMENT,
 *        `first_name` varchar(32) DEFAULT NULL,
 *        `last_name` varchar(32) DEFAULT NULL,
 *        `modified` datetime DEFAULT NULL,
 *        PRIMARY KEY (`id`)
 *    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
 *
 *    CREATE TABLE `points` (
 *        `id` int(11) DEFAULT NULL,
 *        `points` int(11) DEFAULT NULL
 *    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */

$database = new MySQLi(
    "localhost",
    "prophpmvc",
    "prophpmvc",
    "prophpmvc",
    3306
);

$rows = array();
$result = $database->query("SELECT first_name, last_name AS surname, points AS discount FROM users JOIN points ON users.id = points.id WHERE first_name = 'chris' ORDER BY last_name DESC LIMIT 100");

for ($i = 0; $i < $result->num_rows; $i++)
{
    $rows[] = (object) $result->fetch_array(MYSQLI_ASSOC);
}
