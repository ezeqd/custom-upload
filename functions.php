<?php

function accessExist($access, $toCheck){
  return ($access['file_id']==$toCheck['file_id'] && $access['user_id']==$toCheck['user_id']);
}

function loadFiles(){
  global $wpdb;
  $files = $wpdb->get_results("SELECT * FROM wd_cu_files", OBJECT);

  return $files;
}

function getClients(){
  return get_users(['role' => 'customer']);
}

function getProducts(){
  return [
    'sigry' => '1',
    'belen' => '2',
    'bakhou' => '3',
    'lara_teens' => '4',
  ];
}

function getFileType(){
  return [
    'JPG' => '0',
    '1X1' => '1',
    'PDF' => '2',
    'Precios' => '3',
    'Pedidos' => '4',
    'Video' => '5',
  ];
}
function getProductById($id){
  $products = getProducts();
  foreach ($products as $key => $value) {
    if ($value == $id)
      return $key;
  }
}
