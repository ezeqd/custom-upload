
<?php

function cu_load_files_permision_by_user(){
  global $wpdb;

  $params = array();
  parse_str($_POST['user'], $params);
  $user = $params['user'];

  $queryStr = "SELECT wd_uc_files.*, wd_uc_access.access_id, wd_uc_access.user_id FROM wd_uc_files ";
  $queryStr.= "LEFT JOIN wd_ucd_access ON wd_uc_files.file_id=wd_uc_access.file_id AND wd_uc_access.user_id=".$user;

  $access = $wpdb->get_results($queryStr, OBJECT);
?>
  <form action="<?= admin_url('admin-post.php') ?>" method="POST">
    <input type="hidden" name="action" value="assign_permission">
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
              <td><input type="checkbox" name="Capabilities[<?php echo $user ?>][]" value="" <?php echo ($row->user_id != null) ? 'checked': '' ?>></td>
        </tr>
      <?php } ?>
    </table>
    <button type="submit"> Actualizar permisos</button>
  </form>
<?php
  wp_die();
}
?>

<?php
function cu_assign_permission(){
  global $wpdb;
  $permissions = [];
  $capabilities = $_POST['Capabilities'];

  foreach ($capabilities as $userID => $files)
    foreach($files as $index => $fileID)
      $permissions[] = [ 'user_id' => $userID, 'file_id' => $fileID ];


  $stored = $wpdb->get_results("SELECT * FROM wd_cu_access");
  $toDelete = [];
  $deleteFlag = true;
  foreach($stored as $i => $element){
    foreach($permissions as $t => $permission){
      if ( accessExist($permission, $element) ){
        $deleteFlag = false;
        break;
      }
    }
    if ($deleteFlag){
      $toDelete[] = $element;
      unset($stored[$i]);
    }
  }

  $wpdb->delete('wd_cu_access', $toDelete, ['%d', '%d', '%d']);

}

add_action( 'admin_post_assign_permission', 'cu_assign_permission' );

add_action( 'wp_ajax_load_permission', 'cu_load_files_permision_by_user' );
