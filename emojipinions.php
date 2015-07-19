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

  /**
   * Constructor
   */
  function __construct() {
    $this->plugin = new stdClass;
    $this->plugin->name = 'Emojipinions';
    $this->plugin->version = '0.0.1';
    $this->plugin->folder = 'emojipinions';
    $this->plugin->url = plugin_dir_url( __FILE__ ); // Has trailing slash
    $this->plugin->path = plugin_dir_path( __FILE__ ); // Has trailing slash


    add_action( 'admin_enqueue_scripts', array( $this, 'adminEnqueueScriptsAndStyles' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'frontendEnqueueScriptsAndStyles' ) );

    // Actions
    add_action( 'add_meta_boxes', array( $this, 'createMetaBox' ) );
    add_action( 'save_post', array( $this, 'saveMetaBox' ) );

    // Filters
    add_filter( 'the_content', array( $this, 'renderFrontend' ) );
  }

  /**
   * Returns partial templates and renders them
   */
  private function getPartial( $partialPath, $args = array(), $returnTemplate = true) {
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

  public function frontendEnqueueScriptsAndStyles() {
    // CSS

    // JS
    wp_enqueue_script( 'react-js', 'https://cdnjs.cloudflare.com/ajax/libs/react/0.13.3/react.min.js', array(), $this->plugin->version );
    wp_enqueue_script( $this->plugin->folder . '-frontend', $this->plugin->url . 'scripts/frontend.js', array( 'jquery', 'react-js' ), $this->plugin->version, true );
  }

  /**
   * Queues up JavaScript and CSS
   */
  public function adminEnqueueScriptsAndStyles() {
    // CSS
    wp_enqueue_style( $this->plugin->folder . '-jquery-emoji-picker', $this->plugin->url . 'vendor/jquery-emoji-picker/css/jquery.emojipicker.css', array(), $this->plugin->version );
    wp_enqueue_style( $this->plugin->folder . '-jquery-emoji-picker-a', $this->plugin->url . 'vendor/jquery-emoji-picker/css/jquery.emojipicker.a.css', array( $this->plugin->folder . '-jquery-emoji-picker' ), $this->plugin->version );
    wp_enqueue_style( $this->plugin->folder . '-main', $this->plugin->url . 'styles/main.css', array( $this->plugin->folder . '-jquery-emoji-picker-a' ), $this->plugin->version );

    // JS
    wp_enqueue_script( $this->plugin->folder . '-jquery-emoji-picker', $this->plugin->url . 'vendor/jquery-emoji-picker/js/jquery.emojipicker.js', array( 'jquery' ), $this->plugin->version, true );
    wp_enqueue_script( $this->plugin->folder . '-jquery-emoji-picker-a', $this->plugin->url . 'vendor/jquery-emoji-picker/js/jquery.emojipicker.a.js', array( 'jquery' ), $this->plugin->version, true );
    wp_enqueue_script( $this->plugin->folder . '-main', $this->plugin->url . 'scripts/main.js', array( 'jquery' ), $this->plugin->version, true );
  }

  /**
   * Adds the metabox for this plugin
   */
  public function createMetaBox() {
    add_meta_box( 'emojiConfigMetaBox', $this->plugin->name, array( $this, 'emojiConfigMetaBox' ) );
  }

  /**
   * Renders the metabox for this plugin
   */
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
      $emojiMeta[] = array(
        'emoji' => get_post_meta( $post->ID, '_emoji_' . $x, true ),
        'count' => get_post_meta( $post->ID, '_emoji_count_' . $x, true ),
      );
      $x++;
    }
    
    // Render the config box view
    $this->getPartial($this->plugin->path . 'views/metabox.php', array(
      'emoji' => $emojiMeta,
      'hasEmoji' => ($emojiCount > 0 ? true : false),
      'emojiNumber' => 1
    ), false);
  }

  /**
   * Saves the data on post
   */
  public function saveMetaBox( $post_id ) {
    // Ignore autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // Check the nonce to confirm this is a valid request from our plugin
    if ( ( isset ( $_POST[$this->plugin->folder] ) ) && ( ! wp_verify_nonce( $_POST[$this->plugin->folder], plugin_basename( __FILE__ ) ) ) )
      return;

    // Check user permissions
    if ( ( isset ( $_POST['post_type'] ) ) && ( 'page' == $_POST['post_type'] )  ) {
      if ( ! current_user_can( 'edit_page', $post_id ) ) {
        return;
      }    
    } else {
      if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
      }
    }

    // This is a legitimate request, now we can save the data
    if ( isset ( $_POST['_emoji'] ) ) {
      $emojiCount = 0;
      foreach( $_POST['_emoji'] as $key => $emoji ) {
        // @TODO: Validate emoji content
        if($this->isValidEmoji($emoji)) {
          // Update meta
          if( ! empty( $emoji ) ) {
            update_post_meta( $post_id, '_emoji_' . $key, wp_encode_emoji( $emoji ) );

            $emojiCountValue = $_POST['_emoji_count'][$key];
            if(empty($emojiCountValue) || $emojiCountValue < 1) {
              $emojiCountValue = 1;
            }

            update_post_meta( $post_id, '_emoji_count_' . $key, $emojiCountValue );
            $emojiCount++;
          }
        }
      }

      // Store the overall count
      update_post_meta( $post_id, '_emoji_count', $emojiCount );
      
    }
  }

  /**
   * Checks whether or not the supplied input is valid
   */
  private function isValidEmoji( $emoji ) {
    return true;
  }

  public function renderFrontend( $content ) {
    global $post;

    $html = $this->getPartial( $this->plugin->path . 'views/frontend.php', array(
      'post' => $post
    ) );

    $content .= $html;

    return $content;
  }

}

// Initialise the plugin
$EmojipinionsWordPress = new EmojipinionsWordPress;