<?php

require_once(__DIR__ . '/../db/Access.php');

function cu_load_files_permision_by_user(){
    global $wpdb;

    $params = array();
    parse_str($_POST['user'], $params);
    $user = $params['user'];

    if (!$user){ ?>
      <div class="cu-message"><p>No se ha seleccionado ningun cliente</p></div>
    <?php
    wp_die();
    }

    $queryStr = "SELECT wd_cu_files.*, wd_cu_access.access_id, wd_cu_access.user_id FROM wd_cu_files ";
    $queryStr.= "LEFT JOIN wd_cu_access ON wd_cu_files.file_id=wd_cu_access.file_id AND wd_cu_access.user_id=".$user;

    $access = $wpdb->get_results($queryStr, OBJECT);
   ?>
    <form action="<?= admin_url('admin-post.php') ?>" method="POST">
      <input type="hidden" name="action" value="assign_permission">
      <input type="hidden" name="Permissions[user]" value="<?php echo $user ?>">
      <table>
        <tr>
          <th>Archivos</th>
          <th>Permitir Descarga</th>
        </tr>
        <?php foreach($access as $index => $row){
            $lastSlash = strrpos($row->file_dir, '/');
            $filename = substr($row->file_dir, $lastSlash+1);
        ?>
          <tr>
              <td><?php echo $filename ?></td>
              <td><input type="checkbox" name="Permissions[files][]" value="<?php echo $row->file_id?>" <?php echo ($row->user_id != null) ? 'checked': '' ?>></td>
          </tr>
        <?php } ?>
      </table>
      <div class="submitCUButton">
        <button type="submit"> Actualizar permisos</button>
      </div>
    </form>
  <?php
    wp_die();
  }

function prepare_data($req){
  $userID = $req['user'];
  $filesID = $req['files'];

  $permissionArray = [];
  foreach($filesID as $k => $fileID)
    $permissionArray[] = ['file_id' => $fileID, 'user_id' => $userID];

  return $permissionArray;
}

function compare_data($stored, $permissions){

  foreach ($stored as $i => $record) {
    foreach ($permissions as $k => $permission) {
      if ($permission['file_id'] == $record['file_id'] && $permission['user_id'] == $record['user_id']){
        unset($stored[$i]);
        unset($permissions[$k]);
        break;
      }
    }
  }
  return ['toAdd' => $permissions, 'toDelete' => $stored];
}

function delete_permissions($toDelete){
  if (!empty($toDelete)){
    $ids = [];
    foreach ($toDelete as $key => $value) {
      $ids[] = $value['access_id'];
    }
    $ids = implode( ',', $ids );
    return Access::deleteByIDs($ids);
  }
}

function add_permissions($permissions){
  if (!empty($permissions))
    return Access::add($permissions);
  return 0;
}

function cu_assign_permission(){
  global $wpdb;
  $req = $_POST['Permissions'];

  global $wpdb;
  $query = $wpdb->prepare("SELECT * FROM wd_cu_access WHERE user_id=%d", $req['user']);
  $stored = $wpdb->get_results($query, ARRAY_A);

  $permissionsArr = prepare_data($req);
  $comparison = compare_data($stored, $permissionsArr);

  $deleted = delete_permissions($comparison['toDelete']);
  $added = add_permissions($comparison['toAdd']);

  $result =  ($deleted || $added);
  $url ='admin.php?page=global_custom_upload&tab=assignCapabilities&assign_status='.$result;
  wp_redirect($url);
  exit;
}

add_action( 'admin_post_assign_permission', 'cu_assign_permission' );
add_action( 'wp_ajax_load_permission', 'cu_load_files_permision_by_user' );
