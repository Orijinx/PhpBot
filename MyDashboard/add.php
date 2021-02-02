<?php
$name=$_POST["name"];
echo $name;
$mysqli = mysqli_connect("hellohexx.beget.tech", "hellohexx_bot", "12345678Bb", "hellohexx_bot");
$mysqli->query("INSERT INTO notes(nTab) VALUES (`$name`)");

