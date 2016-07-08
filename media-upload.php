<?php
/*
 * Plugin Name: Upload Multiple Files
 * Plugin URI: http://pietaslabs.com
 * Description: Module to Upload Multiple Media Files
 * Version:
 * Author: Subhransu Sekhar
 * Author URI: pietaslabs.com
 * License: GPLv2 or later
 * TextDomain: files-meta
 * DomainPath:
 * Network: true
 */

 $meta_box_media_upload = new Media_Upload();

 class Media_Upload {

 	function __construct() {
 		add_action( 'add_meta_boxes', array( $this, 'setup_media_box' ) );
 		add_action( 'save_post', array( $this, 'save_media_box' ), 10, 2 );
 	}

 	function setup_media_box() {
    $post_types = array( 'post', 'page', );
    foreach( $post_types as $post_type )
     {
         add_meta_box(
             'meta_box_id', // $id
             __( 'Media Upload', 'upload-meta-box' ), // $title
             array( $this, 'media_meta_box_contents' ), // $callback
              $post_type,
             'normal', // $context
             'high' // $priority
         );
     }
   }

 	function media_meta_box_contents() {

 		wp_enqueue_media();
 		wp_enqueue_script( 'upload-meta-box-media', plugins_url('media.js', __FILE__ ), array('jquery') );

 		wp_nonce_field( 'nonce_action', 'nonce_name' );
    $media_fieldcount = get_post_meta( get_the_id(), 'media_fieldcount', true );
 		// one or more
 		$field_names = array( 'media1');
    if(!isset($media_fieldcount) || empty($media_fieldcount)) {
      $media_fieldcount = count($field_names);
    }
    else {
      for($i=2; $i < $media_fieldcount+1; $i++) {
        $field_names[] = 'media'.$i;
      }
    }
    echo "<input type='hidden' id='media_fieldcount'  class='small-text' name='media_fieldcount' value='$media_fieldcount' />";
 		foreach ( $field_names as $name ) {

 			$value = $rawvalue = get_post_meta( get_the_id(), $name, true );

 			$name = esc_attr( $name );
 			$value = esc_attr( $rawvalue );

      echo "<input type='hidden' id='$name-value'  class='small-text'       name='meta-box-media[$name]' value='$value' />";
 			echo "<input type='button' id='$name'        class='button meta-box-upload-button'        value='Upload' />";
 			echo "<input type='button' id='$name-remove' class='button meta-box-upload-button-remove' value='Remove' />";
      if($name == 'media'.$media_fieldcount) {
        echo "<input type='button' id='$name-addmore' class='button meta-box-upload-button-addmore' value='Add More' />";
      }
      $attachment_meta = '';
      if($rawvalue) {
        $attachment_meta = wp_get_attachment_link( $rawvalue );
      }

 			echo "<div class='image-preview'>$attachment_meta</div>";

 			echo '<br />';

 		}

 	}

 	function save_media_box( $post_id, $post ) {
 		if ( ! isset( $_POST['nonce_name'] ) ) //make sure our custom value is being sent
 			return;
 		if ( ! wp_verify_nonce( $_POST['nonce_name'], 'nonce_action' ) ) //verify intent
 			return;
 		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) //no auto saving
 			return;
 		if ( ! current_user_can( 'edit_post', $post_id ) ) //verify permissions
 			return;
    update_post_meta( $post_id, 'media_fieldcount', $_POST['media_fieldcount'] ); //Save Field Count
 		$new_value = array_map( 'intval', $_POST['meta-box-media'] ); //sanitize
 		foreach ( $new_value as $k => $v ) {
 			update_post_meta( $post_id, $k, $v ); //save
 		}

 	}

 }
