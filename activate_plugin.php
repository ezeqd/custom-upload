<?php

register_activation_hook( __FILE__, 'cu_install' );

function cu_create_table(){

    global $wpdb;
    $cudb       = apply_filters( 'cu_database', $wpdb );
    $table_name = $cudb->prefix.'cu_files';
    if( $cudb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {

        $charset_collate = $cudb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            file_id bigint(20) NOT NULL AUTO_INCREMENT,
            file_dir varchar(120) NOT NULL,
            file_type int(10) NOT NULL,
            PRIMARY KEY  (file_id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    $table_name = $cudb->prefix.'cu_access';

    if( $cudb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {

        $charset_collate = $cudb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            access_id bigint(20) NOT NULL AUTO_INCREMENT,
            file_id bigint(20) NOT NULL ,
            user_id bigint(20) NOT NULL,
            download_date timestamp DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (access_id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

    }

    $table_name = $cudb->prefix.'cu_history';

    if( $cudb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {

        $charset_collate = $cudb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            file_id bigint(20) NOT NULL ,
            date timestamp DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

    }

    $table_name = $cudb->prefix.'cu_sucursales';

    if( $cudb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {

        $charset_collate = $cudb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            cliente_id bigint(20) NOT NULL,
            direccion_real varchar(120) NOT NULL,
            direccion_publica varchar(120),
            visibilidad BOOLEAN,
            venta_mayorista BOOLEAN,
            venta_minorista BOOLEAN,
            venta_online BOOLEAN,
            sitio_web BOOLEAN,
            revendedoras BOOLEAN,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

    }
    $table_name = $cudb->prefix.'cu_clientes';

    if( $cudb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {

        $charset_collate = $cudb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            cliente_id bigint(20) NOT NULL AUTO_INCREMENT,
            nombre_cliente varchar(120) NOT NULL,
            PRIMARY KEY  (cliente_id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

    }

}

function cu_install(){
	cu_create_table();
}
