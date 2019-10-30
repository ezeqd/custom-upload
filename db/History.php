<?php

class History{

  const TABLE = 'wd_cu_history';

  static function getAllByUser($id){
    global $wpdb;

    $queryStr = "SELECT wd_cu_history.*, wd_cu_files.file_dir FROM " .History::TABLE;
    $queryStr.= " LEFT JOIN wd_cu_files ON wd_cu_files.file_id=".History::TABLE.".file_id WHERE ".History::TABLE.".user_id=%d";

    $query = $wpdb->prepare($queryStr, array($id));
    return $wpdb->get_results($query, ARRAY_A);
  }

  static function add($params){
    global $wpdb;
    $values = array();

    foreach ( $params as $key => $value )
      $values[] = $wpdb->prepare( "(%d,%d,%d,%s)", $value['id'], $value['user_id'], $value['file_id'], $value['date'] );

    $query = "INSERT INTO " .History::TABLE. " (id, user_id, file_id, date) VALUES ";
    $query .= implode( ",\n", $values );

    return $wpdb->query($query);
  }

}
