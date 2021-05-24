<?php
/**
 * @return PDO
 */
function connectionToDB(): PDO
{
    $st = htmlentities(file_get_contents("../db.json"));
    echo $st;

    $str = str_replace("\n", ' ', $st);
    $str = explode(' ', $str);


    $dsn = "mysql:host=" . $str[2] . ";port=" . $str[5] . ";dbname=" . $str[8] . ";charset=" . $str[11]; //data source name
    $username = $str[14];
    $password = $str[17];


    return new PDO("mysql:host=$str[2];port=$str[5];dbname=$str[8];charset=$str[11]", "$str[14]", "$str[17]");
}
