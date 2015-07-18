<?php
/*
Plugin Name: Emojipinions
Plugin URI:  http://www.github.com/chrishutchinson/emojipinions-wordpress
Description: Add emoji reactions to posts and pages
Version:     0.0.1
Author:      Chris Hutchinson
Author URI:  http://www.github.com/chrishutchinson
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: emojipinions-wordpress
*/

class EmojipinionsWordPress {

  // Init
  function __construct() {
    $this->plugin = new stdClass;
    $this->plugin->name = 'Emojipinions';
    $this->plugin->version = '0.0.1';
    $this->plugin->folder = 'emojipinions';
    $this->plugin->url = plugin_dir_url( __FILE__ ); // Has trailing slash
    $this->plugin->path = plugin_dir_path( __FILE__ ); // Has trailing slash

    // Styles
    add_action( 'admin_enqueue_scripts', array( $this, 'adminEnqueueScriptsAndStyles' ) );

    // Actions
    add_action( 'add_meta_boxes', array( $this, 'createMetaBox' ) );
    add_action( 'save_post', array( $this, 'saveMetaBox' ) );

    // Filters
  }

  public function adminEnqueueScriptsAndStyles() {
    // CSS
    wp_enqueue_style( $this->plugin->folder . '-main', $this->plugin->url . 'styles/main.css', array(), $this->plugin->version );

    // JS
    wp_enqueue_script( $this->plugin->folder . '-main', $this->plugin->url . 'scripts/main.js', array( 'jquery' ), $this->plugin->version );
  }

  public function getPartial( $partialPath, $args = array(), $returnTemplate = true) {
    extract( $args );

    ob_start();
    include( $partialPath );
        $template = ob_get_contents();
        ob_end_clean();

    if ( $returnTemplate ) {
      return $template;
    } else {
      echo $template;
    }
  }

  public function createMetaBox() {
    add_meta_box( 'emojiConfigMetaBox', $this->plugin->name, array( $this, 'emojiConfigMetaBox' ) );
  }

  public function emojiConfigMetaBox( $post ) {
    // Use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), $this->plugin->folder );

    $emojiCount = get_post_meta( $post->ID, '_emoji_count', true );
    if( empty( $emojiCount ) ) {
      $emojiCount = 0;
    }

    $emojiMeta = array();
    $x = 0;
    while( $x < $emojiCount ) {
      $emojiMeta[] = get_post_meta( $post->ID, '_emoji_' . $x, true );
      $x++;
    }
    
    // Render the config box view
    $this->getPartial($this->plugin->path . 'views/emoji_config_box.php', array(
      'emoji' => $emojiMeta,
      'hasEmoji' => ($emojiCount > 0 ? true : false),
      'emojiNumber' => 1
    ), false);
  }

  public function saveMetaBox( $post_id ) {
    // verify if this is an auto save routine. 
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( ( isset ( $_POST[$this->plugin->folder] ) ) && ( ! wp_verify_nonce( $_POST[$this->plugin->folder], plugin_basename( __FILE__ ) ) ) )
      return;

    // Check permissions
    if ( ( isset ( $_POST['post_type'] ) ) && ( 'page' == $_POST['post_type'] )  ) {
      if ( ! current_user_can( 'edit_page', $post_id ) ) {
        return;
      }    
    } else {
      if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
      }
    }

    // OK, we're authenticated: we need to find and save the data
    if ( isset ( $_POST['_emoji'] ) ) {
      $emojiCount = 0;
      foreach( $_POST['_emoji'] as $key => $emoji ) {
        // @TODO: Validate emoji content

        // Update meta
        if( ! empty( $emoji ) ) {
          update_post_meta( $post_id, '_emoji_' . $key, wp_encode_emoji( $emoji ) );
          $emojiCount++;
        }
      }

      update_post_meta( $post_id, '_emoji_count', $emojiCount );
      
    }
  }

}

$EmojipinionsWordPress = new EmojipinionsWordPress;