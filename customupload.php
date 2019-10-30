<?php
/*
Plugin Name: Custom Upload
Plugin URI: http://cu.coodesoft.com.ar
Description: Upload personalizado para proveedores de ropita.
Version: 1.0
Author: Coodesoft
Author URI: http://coodesoft.com.ar
License: GPL2
*/

require_once 'functions.php';

require_once 'upload/tab.php';
require_once 'history/tab.php';
require_once 'permissions/tab.php';
require_once 'sucursales/tab.php';
//require_once 'activate_plugin.php';

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

add_action('admin_enqueue_scripts', 'cu_load_scripts' );
add_action('admin_enqueue_scripts', 'cu_load_stylesheet' );
add_action('admin_menu', 'cu_admin_menu');


function cu_load_scripts() {
  wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyA-sMde0_QIgUq_tMtSqK0RamPViALBZSs', array(), '', true);
	wp_enqueue_script( 'customUploadPanelJS', plugins_url('/js/uploadPanel.js', __FILE__), array('google-maps'), '1.1', true);
}

function cu_load_stylesheet($hook){
	if($hook != 'toplevel_page_global_custom_upload')
  	return;
	wp_enqueue_style( 'customUploadPanelCSS',  plugins_url('/css/uploadPanel.css', __FILE__) );
}

function cu_admin_menu(){
	add_menu_page('Custom Upload', 'Custom Upload', 'manage_options', 'global_custom_upload', 'global_custom_upload_content');
}

function global_custom_upload_content(){


	$screen =  get_current_screen();
	$pluginPageUID = $screen->parent_file;

  ?>
  <div id="customUploadPanel" class="wrap">
      <h3 class="panel-title">Upload de archivos por cliente</h3>

      <h2 class="nav-tab-wrapper">
        <a href="<?= admin_url('admin.php?page='.$pluginPageUID.'&tab=uploadFiles')?>" class="nav-tab">Subir archivos</a>
        <a href="<?= admin_url('admin.php?page='.$pluginPageUID.'&tab=assignCapabilities')?>" class="nav-tab">Asignar Permisos</a>
				<a href="<?= admin_url('admin.php?page='.$pluginPageUID.'&tab=history')?>" class="nav-tab">Historial de Descargas</a>
        <a href="<?= admin_url('admin.php?page='.$pluginPageUID.'&tab=sucursales')?>" class="nav-tab">Sucursales</a>
      </h2>

    <div class="panel-body">
			<?php $activeTab = $_GET['tab']; ?>

			<?php if (!isset($activeTab)){ ?>
      	<div id="uc-tab"><?php	createUploadForn(); ?></div>
			<?php } ?>

			<?php if ($activeTab == 'uploadFiles'){ ?>
				<div class="uc-tab"><?php	createUploadForn(); ?></div>
			<?php } ?>

			<?php if ($activeTab == 'assignCapabilities'){ ?>
				<div class="uc-tab"><?php assignCapabilities(); ?></div>
			<?php } ?>

      <?php if ($activeTab == 'history'){ ?>
				<div class="uc-tab"><?php history(); ?></div>
			<?php } ?>

			<?php if ($activeTab == 'sucursales'){ ?>
				<div class="uc-tab"><?php sucursales(); ?></div>
			<?php } ?>

    </div>
  </div>

<?php
}
