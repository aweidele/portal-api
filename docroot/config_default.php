<?php 
$connection = mysqli_connect("localhost", "lamp", "lamp") or die ("Couldn't connect to server.");
$db = mysqli_select_db($connection,"lamp") or die ("Couldn't select database");