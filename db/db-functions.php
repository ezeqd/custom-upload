<?php

  function delete_access($params){
    $values = array();
    global $wpdb;

    foreach ( $params as $key => $value )
      $values[] = $wpdb->prepare( "(%d,%d)", $value['file_id'], $value['user_id'] );

    $query = "DELETE FROM wd_cu_access (file_id, user_id) VALUES ";
    $query .= implode( ",\n", $values );

    return $wpdb->query($query);
  }

  function delete_access_by_user($id){
    global $wpdb;
    return $wpdb->delete('wd_cu_access', ['user_id' => $id], ['%d']);
  }

  function add_access($params){
    global $wpdb;
    $values = array();

    foreach ( $params as $key => $value )
      $values[] = $wpdb->prepare( "(%d,%d)", $value['file_id'], $value['user_id'] );

    $query = "INSERT INTO wd_cu_access (file_id, user_id) VALUES ";
    $query .= implode( ",\n", $values );

    return $wpdb->query($query);
  }


?>
