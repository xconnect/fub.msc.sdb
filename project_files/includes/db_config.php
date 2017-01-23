<?php

  // database configuration
  $host   = "localhost";   // host
  $port   = "5432";        // port
  $dbname = "spatialdb";   // database name
  $dbuser = "postgres";    // database user
  $dbpass = "postgres";    // database password

  // database connection handle or die('Could not connect: ' . pg_last_error());
  $db = pg_connect("host=$host port=$port dbname=$dbname user=$dbuser password=$dbpass") or die('Could not connect: ' . pg_last_error());

?>
