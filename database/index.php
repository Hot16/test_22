<?php
$_REQUEST['get_data'] = 1;
if (isset($_REQUEST['get_data'])) {
    $data = file_get_contents('data.json');
}
if (isset($_REQUEST['get_names'])) {
    $data = file_get_contents('names.json');
}
echo $data;
die();