<?php
header('Content-Type: application/json');
$output = array();
if(isset($_GET['gender']) && isset($_GET['size']) && isset($_GET['distance']) && isset($_GET['style'])) {
  //$path = __DIR__.'/'.$_GET['size'].'-'.$_GET['gender'].'.csv';
  $path = __DIR__ . '/' . $_GET['style'] . '/' . $_GET['distance'] . '/'.$_GET['size'].'-'.$_GET['gender'].'.csv';
  $values = array();
  if(file_exists($path)) {
    $csv = array_map('str_getcsv', file($path));
    foreach($csv as $line) {
      $string = $line[0];
      $params = explode(';', $string);
      $values[] = $params;
    }
  }
  else {
    $values[] = 'File doesn\'t exist';
  }
}

print json_encode($values);