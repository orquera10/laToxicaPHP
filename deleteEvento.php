<?php
require_once ('config.php');
$id = $_REQUEST['id'];

$sqlDeleteEvento = ("DELETE FROM turnos WHERE  _id='" . $id . "'");
$resultProd = mysqli_query($con, $sqlDeleteEvento);

?>