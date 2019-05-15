<?php
/**
 * Plugin Name:       Content Formatter
 * Plugin URI:        https://pymnts.com
 * Description:       This plugin formats content within a post.
 * Version:           1.0
 * Author:            PYMNTS
 * Author URI:        https://timothyallard.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       content-formatter
 * Domain Path:       /languages
 */

class Content_Formatter {
    public function __construct() {

        /* Include the Advanced Custom Fields (ACF) Plugin Path Dependency */
        include_once( plugin_dir_path( __FILE__ ) . 'vendor/advanced-custom-fields/acf.php' );

        /* Define ACF Pathing and Config. */
        add_filter( 'acf/settings/path', array( $this, 'update_acf_settings_path' ) );
        add_filter( 'acf/settings/dir', array( $this, 'update_acf_settings_dir' ) );
    }

    public function update_acf_settings_path( $path ) {
       $path = plugin_dir_path( __FILE__ ) . 'vendor/advanced-custom-fields/';
       return $path;
    }

    public function update_acf_settings_dir( $dir ) {
       $dir = plugin_dir_url( __FILE__ ) . 'vendor/advanced-custom-fields/';
       return $dir;
   }


   /**
   *  WP Query
   *
   * The query arguments
   * We want to find recent posts, that have not been modified in the past.
   * Lets check for a meta key.
   * If present, skip and go to the next.
   * Lets limit this to one post.
   *
   */

   public function find_posts()
   {
     $args = [
        'post_type'        => 'post',
        'cat'              => 3,
         'posts_per_page'   => 1,
        'orderby'          => 'date',
         'order'            => 'ASC'
     ];

     // The post query
     $query = new WP_Query( $args );

     // If results
     if ( $query->have_posts() ) {

         // While it has results
         while ( $query->have_posts() ) {

             // The post
             $query->the_post();

             // now $query->post is WP_Post Object, use:
             // $query->post->ID, $query->post->post_title, etc.

             $the_post_content = get_post_field('post_content', $post_id);

             // Run first regex and clean it up.
             $first_modificaion($the_post_content);
         }

     }

     function first_modification($data)
     {
       $cleaned_data = "";
       return $cleaned_data;
     }

     function content_formatter_pre_render_filter( $content ) {
          // Do stuff to $content, which contains the_content()
          // Then return it
          return $content;
     }
     add_filter( 'the_content', 'content_formatter_pre_render_filter' );

     /* Silence
     add_filter('the_content',function($the_content){
         // find blockquotes
         $regex = '/<blockquote>(.+?)<\/blockquote>([\n|$])/i';
         $blockquotes = preg_match_all($regex,$the_content,$matches);

         // remove blockquotes
         $main_content = preg_replace($regex,'',$the_content);

         // rebuild blockqoutes
         $my_blockquotes = '';
         foreach ($matches[1] as $blockquote) {
             $my_blockquotes .= "<blockquote>{$blockquote}</blockquote>";
         }

         // rebuild content
         $new_content = '';
         if (!empty($my_blockquotes)) {
             $new_content = "
             <div class='my-blockquotes'>
                 {$my_blockquotes}
             </div>\n";
         }
         $new_content .= "
         <div class='main-content'>
             {$main_content}
         </div>\n";

         return $new_content;
     });
     */
   }
}

new Content_Formatter();
