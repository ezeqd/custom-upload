<?php
require_once(__DIR__ . '/../db/Clients.php');
/* Con esta variable se puede sustituir facilmente la ruta base de las imágenes
   sin tener que modificar TODAS las rutas, una por una. */
$imgBasePath = home_url('/img/');
function getUrlWithProtocol($text){
    $http = strpos($text, 'http');
    $https = strpos($text, 'https');
    if ($https !== false || $http !== false)
        return $text;

    return 'http://' . $text;
}
function buildLocalesListHTML($sucursales){
  global $imgBasePath;
  $groupedSucursales = [];
  foreach ($sucursales as $key => $sucursal) {
    $ciudad = $sucursal['ciudad'];
    $groupedSucursales[$ciudad][] = $sucursal;
  }
  foreach ($groupedSucursales as $ciudad => $sucursales) { ?>
    <div class="container-prov">
      <div class="ciudad bk-pointer"> <?php echo $ciudad ?> </div>
      <?php foreach ($sucursales as $k => $v) { ?>
        <div id="hidden-info" class="sucursal">
          <div class="nombre_cliente" data-id="<?php echo $v['id'] ?>" data-address="<?php echo $v['direccion_publica'].",".$v['ciudad'].",".$v['provincia'].",Argentina" ?>">
             <span><?php echo $v['nombre_cliente'] ?></span>
          </div>
          <div class="direccion_publica"> <?php echo $v['direccion_publica'] ?> </div>
          <?php if ( strlen($v['telefono']) ) { ?>
              <div class="telefono">
                  <img class="items imgtel" data-toggle="tooltip" data-placemen="top" title="Teléfono" src="<?php echo $imgBasePath.'phone.svg'?>">
                  <p class="numtel">
                    <?php echo $v['telefono'] ?>
                  </p>
              </div>
          <?php } ?>

          <div class="info">
            <ul>
              <?php if ( strlen($v['sitio_web']) ) { ?>
                <li>
                    <a href="<?php echo getUrlWithProtocol($v['sitio_web']) ?>" target="_blank">
                        <img class="items" data-toggle="tooltip" data-placement="top" title="Sitio Web" src="<?php echo $imgBasePath.'locales_sitio_web.svg'?>">
                    </a>
                </li>
              <?php } ?>

              <?php if (($v['venta_mayorista'])== true) { ?>
                <li><img class="items" data-toggle="tooltip" data-placement="top" title="Venta Mayorista" src="<?php echo $imgBasePath.'locales_venta_mayorista.svg'?>"></li>
              <?php } ?>

              <?php if (($v['venta_minorista'])== true) {  ?>
                <li><img class="items" data-toggle="tooltip" data-placement="top" title="Venta Minorista" src="<?php echo $imgBasePath.'locales_venta_minorista.svg'?>"></li>
              <?php } ?>

              <?php if (($v['venta_online'])== true) { ?>
                <li><img class="items" data-toggle="tooltip" data-placement="top" title="Venta Online" src="<?php echo $imgBasePath.'locales_venta_online.svg'?>"></li>
              <?php } ?>

              <?php if (($v['revendedoras'])== true) { ?>
                <li><img class="items" data-toggle="tooltip" data-placement="top" title="Revendedor" src="<?php echo $imgBasePath.'locales_revendedoras.svg'?>"></li>
              <?php } ?>
            </ul>
          </div>
          <div class="fusion-separator fusion-full-width-sep sep-single sep-solid separator"></div>
        </div>
      <?php }?>
    </div>
  <?php }
}
add_action( 'wp_ajax_cu_add_client', 'cu_add_client' );
function cu_add_client(){
  $params = array();
  parse_str($_POST['data'], $params);
  $clientName = $params['Cliente'];
  $clientName = $clientName;
  $stored = Clients::getByName($clientName);
  if ($stored){
    $msg = 'El cliente ya existe mostri';
    echo json_encode(['msg' => $msg, 'type' => 'cu-error']);
    wp_die();
  }
  $result = Clients::add($clientName);
  if ($result){
    $stored = Clients::getAll();
    $html = "";
    foreach ($stored as $key => $value) {
      $html .= '<tr>';
      $html .= '<td>' . $value['nombre_cliente'] . '</td>';
      $html .= '<td>  <div class="edit-client"></div><div class="remove-client"></div></td>';
      $html .= '</tr>';
    }
    echo json_encode(['msg' => 'Se añadió correctamente el cliente',
                      'response' => $html,
                      'type' => 'cu-success'
                    ]);
    wp_die();
  }
}
add_action( 'wp_ajax_cu_edit_client', 'cu_edit_client' );
function cu_edit_client(){
  $params = array();
  parse_str($_POST['data'], $params);
  $clientEdit = $params['ClientEdit'];
  $clientName = $clientEdit['name'];
  $clientId = $clientEdit['id'];
  $result = Clients::update($clientId, $clientName);
  if ($retuls !== false){
    echo json_encode(['msg' => 'Se actualizó correctamente el cliente',
                      'response' => $clientName,
                      'type' => 'cu-success']);
  } else{
    echo json_encode(['msg' => 'Se produjo un error al actualizar el cliente',
                      'response' => $clientName,
                      'type' => 'cu-error']);
  }
  wp_die();
}
add_action( 'wp_ajax_cu_delete_client', 'cu_delete_client' );
function cu_delete_client(){
  $params = array();
  parse_str($_POST['data'], $params);
  $toRemove = $params['ClientRemove'];
  $result = Clients::delete($toRemove);
  if ($result){
    echo json_encode(['msg' => 'Se eliminó correctamente el cliente',
                      'type' => 'cu-success']);
  } else{
    echo json_encode(['msg' => 'Se produjo un error al eliminar el cliente',
                      'type' => 'cu-error']);
  }
  wp_die();
}
add_action( 'wp_ajax_cu_get_sucursales', 'cu_get_sucursales' );
function cu_get_sucursales(){
  $params = array();
  parse_str($_POST['user'], $params);
  $cliente_id = $params['Sucursal']['cliente_actual'];
  $sucursales = Clients::getSucursalesByClient($cliente_id);
  if (!empty($sucursales)){
    $html = "";
    foreach ($sucursales as $key => $value)
      $html .= '<li>' . $value['direccion_publica'] . '</li>';
    echo $html;
  }else
    echo 'No hay sucursales cargadas';
  wp_die();
}
add_action( 'wp_ajax_cu_add_sucursal', 'cu_add_sucursal' );
function cu_add_sucursal(){
  $params = array();
  parse_str($_POST['data'], $params);
  $sucursal = $params['Sucursal']['location'];
  $cliente_id = $params['Sucursal']['cliente_actual'];
  $provincia = $params['Sucursal']['provincia'];
  $ciudad = $params['Sucursal']['ciudad'];
  $result = Clients::addSucursal($cliente_id, $sucursal, $provincia, $ciudad);
  if ($result){
    $sucursales = Clients::getSucursalesByClient($cliente_id);
    if (!empty($sucursales)){
      $html = "";
      foreach ($sucursales as $key => $value)
        $html .= '<li>' . $value['direccion_publica'] . '</li>';
    }
    echo json_encode(['msg' => 'Se añadió correctamente el cliente',
                      'response' => $html,
                      'type' => 'cu-success'
                    ]);
  } else{
    $msg = 'Se produjo un error al agregar la sucursal. Consulte con soporte';
    echo json_encode(['msg' => $msg, 'type' => 'cu-error']);
  }
  wp_die();
}
add_action( 'wp_ajax_cu_get_all_sucursales', 'cu_get_all_sucursales' );
function cu_get_all_sucursales(){
  $sucursales = Clients::getSucursales();
  echo json_encode($sucursales);
  wp_die();
}
add_action( 'wp_ajax_cu_get_geocode_sucursales', 'cu_get_geocode_sucursales' );
add_action( 'wp_ajax_nopriv_cu_get_geocode_sucursales', 'cu_get_geocode_sucursales' );
function cu_get_geocode_sucursales(){
  $ids = $_POST['geodata'];
  if (count($ids)){
    $sucursales = Clients::getGeocodeSucursales($ids);
    echo json_encode($sucursales);
  }
  wp_die();
}
add_action('wp_ajax_load_prov', 'load_prov');
add_action('wp_ajax_nopriv_load_prov', 'load_prov');
function load_prov(){
  parse_str ($_POST['user'], $values);
  $sucursales = Clients::getSucursalesByProvincia($values['menu-prov']);
  echo buildLocalesListHTML($sucursales);
  wp_die();
}
add_action('wp_ajax_cat_filter', 'cat_filter');
add_action('wp_ajax_nopriv_cat_filter', 'cat_filter');
function cat_filter(){
  $category = $_POST['cat'];
  $sucursales = Clients::getSucursalesByCategory($category);
  echo buildLocalesListHTML($sucursales);
  wp_die();
}
add_action( 'wp_ajax_cu_edit_features', 'cu_edit_features' );
function cu_edit_features(){
  parse_str($_POST['data'], $params);
  $clientes = $params['Cliente'];
  $result = [];
  $results = [];
  $specialKeys = Clients::getSpecialKeys();

  foreach ($clientes as $cliente_id => $sucursales) {
    foreach ($sucursales as $sucursal_id => $features) {
      $fields = [];
      $fields['visibilidad'] = 0;
      $fields['venta_mayorista'] = 0;
      $fields['venta_minorista'] = 0;
      $fields['venta_online'] = 0;
      $fields['revendedoras'] = 0;

      foreach ($features as $key => $value) {
        if ( in_array($key, $specialKeys) )
          $fields[$key] = $value;
        else
          $fields[$key] = 1;
      }
      $result = Clients::updateSucursalFeature($fields, $cliente_id, $sucursal_id);
      $results[] = [$cliente_id, $sucursal_id, $result];
    }
  }
  $msg = "";
  $return = [];
  foreach ($results as $key => $result) {
    if ($result === false)
      $msg .= 'Se produjo un error en el cliente con id: '. $result[0] ."y sucursal con id: ". $sucursal_id. '<br>';
  }
  if (strlen($msg) == 0){
    $msg = 'Se actualizaron las características exitosamente';
    $return['type'] = 'cu-success';
  } else
    $return['type'] = 'cu-error';
  $return['response'] = '<p>'. $msg .'</p>';
  echo json_encode($return);
  wp_die();
}
add_action('wp_ajax_cu_geocode_sucursales', 'cu_geocode_sucursales' );
function cu_geocode_sucursales(){
  $results = $_POST['data'];
  $errors = [];
  foreach ($results as $key => $result) {
    $status = Clients::updateSucursalGeocode($result);
    if ($status === false){
      $errors[] = ['id' => $result[0]];
    }
  }
  echo json_encode(['response' => $errors]);
  wp_die();
}
