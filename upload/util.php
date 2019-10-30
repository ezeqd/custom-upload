<?php



function get_cu_upload_folder(){
  $plugin_path = plugin_dir_path(__FILE__);

  $lastSlash = strrpos($plugin_path, '/');
  $tmpPath = substr($plugin_path, 0, $lastSlash);

  $lastSlash = strrpos($tmpPath, '/');
  $filename = substr($tmpPath, 0, $lastSlash);

  return $filename."/files";
}

function navigate($path){

  if (is_dir($path)) {
    if ($dh = opendir($path)) {
      $result = [];
      while (($file = readdir($dh)) !== false) {

        if ($file!="." && $file!=".."){
          if (is_dir($path ."/".$file))
            $result['dir'][] = $file;
          else{
            $result['file'][] = $file;
          }
        }
      }
      closedir($dh);
      return $result;
    }
  }
  return false;
}
