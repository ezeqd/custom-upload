<?php
require_once(__DIR__ . '/../db/History.php');

function cu_load_history_by_user(){
  $params = array();
  parse_str($_POST['user'], $params);
  $user = $params['user'];

  if (!$user){ ?>
    <div class="cu-message"><p>No se ha seleccionado ningun cliente</p></div>
  <?php
  wp_die();
  }

  $rows = History::getAllByUser($user);
  if (empty($rows)) { ?>
    <div class="cu-message"><p>El usuario no realizó ninguna descarga</p></div>
  <?php
  wp_die();
  }
?>

  <table>
    <tr>
      <th>Archivo</th>
      <th>Fecha de descarga</th>
    </tr>
    <?php foreach ($rows as $key => $record):
      $lastSlash = strrpos($record['file_dir'], '/');
      $filename = substr($record['file_dir'], $lastSlash+1);
      ?>
      <tr>
        <td><?php echo $filename ?></td>
        <?php $date = new DateTime($record['date']); ?>
        <td><?php echo $date->format('Y-m-d H:i:s'); ?> </td>
      </tr>
    <?php endforeach; ?>
  </table>


<?php
  wp_die();
  }

add_action( 'wp_ajax_load_history', 'cu_load_history_by_user' );
