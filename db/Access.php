<?php

class Access{

  const TABLE = 'wd_cu_access';

  static function deleteByIDs($IDs){
    $values = array();
    global $wpdb;

    $query = "DELETE FROM " .Access::TABLE. " WHERE access_id IN ($IDs)";
    return $wpdb->query($query);
  }

  static function deleteByUser($id){
    global $wpdb;
    return $wpdb->delete(Access::TABLE, ['user_id' => $id], ['%d']);
  }

  static function add($params){
    global $wpdb;
    $values = array();

    foreach ( $params as $key => $value )
      $values[] = $wpdb->prepare( "(%d,%d)", $value['file_id'], $value['user_id'] );

    $query = "INSERT INTO " .Access::TABLE. " (file_id, user_id) VALUES ";
    $query .= implode( ",\n", $values );

    return $wpdb->query($query);
  }
}
