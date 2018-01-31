<?php
    $host = "sql12.freemysqlhosting.net";
    $username = "sql12218252";
    $password = "ARBn1864yi";

   $conn = mysqli_connect($host,$username, $password);
   if (!$conn) {
       die("Connection failed: " . mysqli_connect_error());
   }
   echo "Connected successfully";
   ?>
