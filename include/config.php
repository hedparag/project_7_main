<?php
$host="localhost";
$port="5432";
$dbname="EmpDataB";
$user="postgres";
$password="pgadmin";
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if(!$conn){
    die("Connection failed: " . pg_last_error());
}
?>