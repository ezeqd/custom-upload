<?php

require_once('history.php');

function history(){
  $users = getClients();
?>


  <div id="clientSelection">
        <div>Seleccione el cliente:</div>

        <form id="downloadsByClientForm">
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

  <div id="filesDownloadsTable"></div>

<?php }
