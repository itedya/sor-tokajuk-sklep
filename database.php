<?php
// Create connection
$conn = new mysqli("localhost", "sklep", "", "sklep");

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

return $conn;
?>