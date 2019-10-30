<?php
class Clients{
    const TABLE = 'wd_cu_clientes';
    const RELATED = 'wd_cu_sucursales';
    
  static function getSpecialKeys(){
      return ['direccion_publica', 'sitio_web', 'telefono'];
  }    
    
  static function add($name){
    global $wpdb;
    return $wpdb->insert(self::TABLE, array('nombre_cliente' => $name), array('%s') );
  }
  static function update($id, $name){
    global $wpdb;
    return $wpdb->update(self::TABLE, ['nombre_cliente' => $name], ['cliente_id' => $id], ['%s'], ['%d']);
  }
  static function delete($id){
    global $wpdb;
    $wpdb->query('START TRANSACTION');
    $result = $wpdb->delete( self::TABLE, ['cliente_id' => $id], ['%d'] );
    $related = $wpdb->delete( self::RELATED, ['cliente_id' => $id], ['%d'] );
    if ($result !== false && $related !== false){
      $wpdb->query('COMMIT');
      return true;
    } else{
      $wpdb->query('ROLLBACK');
      return false;
    }
  }
  static function addSucursal($cliente_id, $sucursal, $provincia, $ciudad){
   global $wpdb;
   $values = array( 'cliente_id' => $cliente_id,
                    'provincia' => $provincia,
                    'ciudad' => $ciudad,
                    'direccion_real' => $sucursal,
                    'direccion_publica' => $sucursal );
   $types = array( '%d', '%s', '%s', '%s', '%s' );
   return $wpdb->insert(self::RELATED, $values, $types);
  }
  static function updateSucursalFeature($params, $cliente_id, $sucursal_id){
    global $wpdb;
    $fields = [];
    $types = [];
    $specialKeys = self::getSpecialKeys();
    foreach ($params as $key => $value) {
      $types[] = ( in_array($key, $specialKeys) ) ? '%s' : '%d';
    }
    return $wpdb->update(self::RELATED, $params, ['id' => $sucursal_id, 'cliente_id' => $cliente_id], $types, ['%d', '%d']);
  }
  static function updateSucursalGeocode($params){
    global $wpdb;
    return $wpdb->update(self::RELATED, ['lat' => $params[1], 'long' => $params[2]], ['id' => $params[0]], ['%s'], ['%s']);
  }
  static function getAll(){
    global $wpdb;
    $queryStr = 'SELECT * FROM '. self::TABLE .' ORDER BY cliente_id ASC';
    return $wpdb->get_results($queryStr, ARRAY_A);
  }
  static function getByName($name){
    global $wpdb;
    $queryStr = 'SELECT * FROM '. self::TABLE .' WHERE nombre_cliente=%s';
    $query = $wpdb->prepare($queryStr, array($name));
    return $wpdb->get_results($query, ARRAY_A);
  }
  static function getSucursalesByClient($id){
    global $wpdb;
    $queryStr = 'SELECT * FROM '. self::TABLE;
    $queryStr.= ' RIGHT JOIN '. self::RELATED .' ON '. self::TABLE .'.cliente_id='. self::RELATED .'.cliente_id';
    $queryStr.= ' WHERE '. self::TABLE .'.cliente_id=%d';
    $query = $wpdb->prepare($queryStr, array($id));
    return $wpdb->get_results($query, ARRAY_A);
  }
  static function getSucursales(){
    global $wpdb;
    $queryStr = 'SELECT * FROM '. self::TABLE;
    $queryStr.= ' RIGHT JOIN '. self::RELATED .' ON '. self::TABLE .'.cliente_id='. self::RELATED .'.cliente_id';
    return $wpdb->get_results($queryStr, ARRAY_A);
  }
  static function getGeocodeSucursales($ids){
    global $wpdb;
    $placeholders = array_fill(0, count($ids), '%d');
    $format = implode(', ', $placeholders);
    $queryStr = 'SELECT id, ' .self::RELATED. '.lat, '.self::RELATED.'.long, '.self::RELATED.'.telefono FROM '. self::RELATED .' WHERE id IN ('.$format.')';
    return $wpdb->get_results($wpdb->prepare($queryStr, $ids), ARRAY_A);
  }
  static function getSucursalesByProvincia($provincia){
    global $wpdb;
    $queryStr = 'SELECT * FROM ' .self::RELATED;
    $queryStr.= ' LEFT JOIN '. self::TABLE .' ON '. self::TABLE .'.cliente_id='. self::RELATED.'.cliente_id WHERE provincia ="'. $provincia.'"';
    /*$str = $wpdb->esc_like($provincia);
    $str = '%' . $str . '%';*/
    $query = $wpdb->prepare($queryStr, array($provincia));
    return $wpdb->get_results($queryStr, ARRAY_A);
  }
  static function getSucursalesByCategory($category){
    global $wpdb;
    $queryStr = 'SELECT * FROM ' .self::RELATED;
    $queryStr.= ' LEFT JOIN '. self::TABLE .' ON '. self::TABLE .'.cliente_id='. self::RELATED.'.cliente_id WHERE '. $category.' = 1';
    $query = $wpdb->prepare($queryStr, array($category));
    return $wpdb->get_results($queryStr, ARRAY_A);
  }
}