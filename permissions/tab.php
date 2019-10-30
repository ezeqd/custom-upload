<?php

require_once('permission.php');


function assignCapabilities(){
  $users = getClients();

?>
  <div id="ucInstructions">
    <p>Seleccione un cliente y luego haga click en cada checkbox para indicar que se otorga el permiso de descarga. </p>
  </div>

  <?php if (isset($_GET['assign_status'])){ ?>
  <div id="actionResult">
    <p><?php echo $result = $_GET['assign_status'] ? 'Se modificaron los permisos de descarga exitosamente':'Se produjo un error inesperado al modificar los permisos de descarga'?></p>
  </div>
  <?php } ?>

  <div id="clientSelection">
      <div>Seleccione el cliente:</div>

      <form id="filesByClientForm">
        <div id="clientsList">
          <select name="user" required>
              <option value="" disabled selected>Seleccione un cliente</option>
            <?php foreach ($users as $key => $user) { ?>
              <option value="<?php echo $user->ID?>"><?php echo $user->display_name ?></option>
            <?php } ?>
          </select>
        </div>
        <div>
          <button type="submit" name="button">Seleccionar</button>
        </div>
    </form>
  </div>

  <div class="uc-horizontal-separator"></div>

  <div id="filesPermissionTable"></div>

<?php }
