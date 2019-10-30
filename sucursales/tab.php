<?php
require_once 'sucursales.php';
function sucursales(){
  $screen =  get_current_screen();
	$pluginPageUID = $screen->parent_file;
?>
  <h2 class="nav-tab-wrapper">
    <a href="<?= admin_url('admin.php?page='.$pluginPageUID.'&tab=sucursales&action=user')?>" class="nav-tab">Cargar clientes</a>
    <a href="<?= admin_url('admin.php?page='.$pluginPageUID.'&tab=sucursales&action=upload')?>" class="nav-tab">Cargar sucursal</a>
    <a href="<?= admin_url('admin.php?page='.$pluginPageUID.'&tab=sucursales&action=edit')?>" class="nav-tab">Editar características</a>
    <a href="<?= admin_url('admin.php?page='.$pluginPageUID.'&tab=sucursales&action=geocode')?>" class="nav-tab">Geolocalizar Sucursales</a>
  </h2>

  <div class="panel-body">
    <?php
    $activeTab = $_GET['tab'];
    $action = $_GET['action'];
    if ($activeTab == 'sucursales' && !isset($action))
      createCliente();
    if ($activeTab == 'sucursales' && $action == 'user')
      createCliente();
    if ($activeTab == 'sucursales' && $action == 'upload')
      uploadSucursal();
    if ($activeTab == 'sucursales' && $action == 'edit')
      editFeatures();
    if ($activeTab == 'sucursales' && $action == 'geocode')
      geocodeSucursales();
    ?>
  </div>
<?
}
function geocodeSucursales(){ ?>
  <div id="ucInstructions">
    <p>Este proceso es complejo y puede llevar algunos minutos</p>
  </div>
  <div id="actionResult"  class="hidden"></div>
  <div id="geocodeSucursal">
     <button id="initGeocode" type="button">Iniciar proceso de geolocalización</button>
    <div class="geocode-progress">
      <div class="headerProgress"></div>
      <div class="bodyProgress"></div>
    </div>
  </div>
<?php
}
function createCliente(){ ?>
  <div id="ucInstructions">
    <p>Ingrese el nombre del cliente que desea agregar.
       Luego diríjase a la pestaña "Cargar sucursal" para cargar las sucursales asociadas
    </p>
  </div>

  <div id="actionResult"  class="hidden"></div>

  <div id="uploadSucursal">
    <div class="left-panel">
      <form id="newClientForm" class="form" method="post">

        <div id="newClientInput" class="form-group cu-form-group">
           <label>Nombre del cliente:  </label>
           <input type="text" class="form-control" name="Cliente" placeholder="ej. Ipanema">
         </div>
         <div class="form-group cu-form-group">
           <button type="submit">Agregar cliente</button>
           <div class="cu-loader"></div>
         </div>
      </form>
    </div>
    <div class="right-panel">
      <?php
       $clientes = Clients::getAll();
      ?>
      <h3>Clientes cargados</h3>
      <div id="newClientsTable">
        <table>
          <?php foreach ($clientes as $key => $value) { ?>
            <tr>
              <td id="cliente_<?php echo $value['cliente_id']?>" class="client-name"><?php echo $value['nombre_cliente']?></td>
              <td class="edit-client-name">
                <form id="editClientName">
                  <input class="cu-inline-element" type="text" name="ClientEdit[name]" placeholder="<?php echo $value['nombre_cliente']?>">
                  <input type="hidden" name="ClientEdit[id]" value="<?php echo $value['cliente_id']?>">
                  <i class="fa fa-window-close fa-2x cancel-edit-client cu-inline-element" aria-hidden="true"></i>
                  <i class="fa fa-check-square fa-2x confirm-edit-client cu-inline-element" aria-hidden="true"></i>
                </form>
              </td>
              <td class="actions">
                <div>
                  <i class="fa fa-pencil fa-2x edit-client" aria-hidden="true"></i>
                  <form id="deleteClient" class="cu-inline-element" data-delete="<?php echo $value['cliente_id']?>">
                    <input type="hidden" name="ClientRemove" value="<?php echo $value['cliente_id']?>">
                    <i class="fa fa-trash fa-2x delete-client"  aria-hidden="true"></i>
                  </form>
                </div>
              </td>
            </tr>
          <?php } ?>
        </table>
      </div>
    </div>
  </div>
<?php
}
function uploadSucursal(){?>
  <div id="uploadSucursal">

    <div id="ucInstructions">
      <p>
        <ul>
          <li>Seleccione un cliente para ver las sucursales cargadas</li>
          <li>Ingrese una dirección para cargar una nueva sucursal del cliente seleccionado</li>
        </ul>
     </p>
    </div>
    <div id="actionResult" class="hidden"></div>

    <div class="left-panel">

      <form id="uploadSucursalForm" class="form" method="post">

          <div id="sucursalClientSelection" class="cu-form-group form-group">
              <div>Seleccione el cliente:</div>
              <select name="Sucursal[cliente_actual]" required>
                  <option id="noClient" value="" disabled selected>Cliente</option>
                  <?php $clientes = Clients::getAll(); ?>
                  <?php foreach ($clientes as $key => $value) { ?>
                  <option value="<?php echo $value['cliente_id'] ?>"> <?php echo $value['nombre_cliente'] ?></option>
                  <?php } ?>
              </select>
          </div>
          <div class="form-group cu-form-group">
              <label for="sucursal">Dirección:</label>
              <input id="sucursalInput" type="text" class="form-control" name="Sucursal[location]" placeholder="ej. Calle Falsa 123" required>
          </div>
          <div class="form-group cu-form-group">
              <label for="provincia">Provincia:</label>
              <select id="provincia" name="Sucursal[provincia]" >
                  <option value="" selected>Todas</option>
    							<option value="Buenos Aires" >Buenos Aires</option>
    							<option value="Catamarca" >Catamarca</option>
    							<option value="Chaco" >Chaco</option>
    							<option value="Chubut" >Chubut</option>
    							<option value="Cordoba" >Cordoba</option>
    							<option value="Corrientes" >Corrientes</option>
    							<option value="Entre Rios" >Entre Rios</option>
    							<option value="Formosa" >Formosa</option>
    							<option value="Jujuy" >Jujuy</option>
    							<option value="La Pampa" >La Pampa</option>
    							<option value="La Rioja" >La Rioja</option>
    							<option value="Mendoza" >Mendoza</option>
    							<option value="Misiones" >Misiones</option>
    							<option value="Neuquen" >Neuquen</option>
                                <option value="Rio Negro" >Rio Negro</option>
                                <option value="Salta" >Salta</option>
    							<option value="San Juan" >San Juan</option>
    							<option value="San Luis" >San Luis</option>
    							<option value="Santa Cruz" >Santa Cruz</option>
    							<option value="Santa Fe" >Santa Fe</option>
    							<option value="Santiago del Estero" >Santiago del Estero</option>
    							<option value="Tierra del Fuego" >Tierra del Fuego</option>
    							<option value="Tucuman" >Tucuman</option>
              </select>
          </div>
          <div class="form-group cu-form-group">
              <label for="ciudad">Ciudad:</label>
              <input id="ciudadInput" type="text" class="form-control" name="Sucursal[ciudad]" placeholder="ej. Tandil" required>
          </div>

          <div id="uploadBtn" class="cu-form-group form-group">
              <button type="submit">Cargar sucursal</button>
              <div class="cu-loader"></div>
          </div>
      </form>
    </div>

    <div class="right-panel">
      <h3>Sucursales Cargadas</h3>
      <div class="cu-loadIndicator"></div>
      <div class="uc-list"><ul></ul></div>
    </div>
  </div>
<?php
}
function editFeatures(){?>
  <div id="editFeatures">
    <div id="ucInstructions">
      <p>Edite las características de las sucursales checkeando las casillas</p>
    </div>
    <div id="actionResult" class="hidden"></div>

    <form id="editFeaturesForm">
      <table>
        <tr>
          <th>Cliente</th>
          <th>Dirección</th>
          <th>Sitio Web</th>
          <th>Telefono</th>
          <th>Visibilidad</th>
          <th>Venta Mayorista</th>
          <th>Venta Minorista</th>
          <th>Venta online</th>
          <th>Revendedoras</th>
        </tr>
        <?php $sucursales = Clients::getSucursales(); ?>
        <?php foreach ($sucursales as $key => $sucursal) { ?>
        <tr>
          <td><?php echo $sucursal['nombre_cliente'] ?></td>
          <td><input name="Cliente[<?php echo $sucursal['cliente_id'] ?>][<?php echo $sucursal['id'] ?>][direccion_publica]" "type="text" value="<?php echo $sucursal['direccion_publica'] ?>"></td>
          <td><input name="Cliente[<?php echo $sucursal['cliente_id'] ?>][<?php echo $sucursal['id'] ?>][sitio_web]" type="text" value="<?php echo $sucursal['sitio_web'] ?>" ></td>
          <td><input name="Cliente[<?php echo $sucursal['cliente_id'] ?>][<?php echo $sucursal['id'] ?>][telefono]" type="text" value="<?php echo $sucursal['telefono'] ?>" ></td>
          <td><input name="Cliente[<?php echo $sucursal['cliente_id'] ?>][<?php echo $sucursal['id'] ?>][visibilidad]" type="checkbox" <?php echo $sucursal['visibilidad'] ? 'checked' : '' ?> ></td>
          <td><input name="Cliente[<?php echo $sucursal['cliente_id'] ?>][<?php echo $sucursal['id'] ?>][venta_mayorista]" type="checkbox" <?php echo $sucursal['venta_mayorista'] ? 'checked' : '' ?> ></td>
          <td><input name="Cliente[<?php echo $sucursal['cliente_id'] ?>][<?php echo $sucursal['id'] ?>][venta_minorista]" type="checkbox" <?php echo $sucursal['venta_minorista'] ? 'checked' : '' ?> ></td>
          <td><input name="Cliente[<?php echo $sucursal['cliente_id'] ?>][<?php echo $sucursal['id'] ?>][venta_online]" type="checkbox" <?php echo $sucursal['venta_online'] ? 'checked' : '' ?> ></td>
          <td><input name="Cliente[<?php echo $sucursal['cliente_id'] ?>][<?php echo $sucursal['id'] ?>][revendedoras]" type="checkbox" <?php echo $sucursal['revendedoras'] ? 'checked' : '' ?> ></td>
        </tr>
        <?php } ?>
      </table>
      <div class="cu-submit-button cu-text-center">
        <button type="submit">Editar características</button>
        <div class="cu-loader"></div>
      </div>
    </form>
  </div>
<?php
}