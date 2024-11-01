<?php

/*
Plugin Name: Smart Post Grid Widget
Plugin URI: http://manhuset.se/
Description: A post listing widget.
Tags: Post grid, grid, post, list, widget
Version: 1.0.0
Author: Kjeld Hansen
Author URI: #
Requires at least: 4.0
Tested up to: 4.7
Text Domain: smart-post-grid-widget
*/

if ( ! defined( 'ABSPATH' ) ) exit; 
 
add_action( 'widgets_init', 'postGrid_widgets_init');

function postGrid_widgets_init(){
	register_widget( "postGrid_featured_posts_widget" );
}

/**
 * Featured Posts widget
 */
class postGrid_featured_posts_widget extends WP_Widget {

   function __construct() {
      $widget_ops = array( 'classname' => 'widget_featured_posts widget_featured_meta', 'description' =>__( 'Display latest posts or posts of specific category.' , 'postGrid') );
      $control_ops = array( 'width' => 200, 'height' =>250 );
      parent::__construct( false,$name= __( 'Posts Grid', 'postGrid' ),$widget_ops);
   }

   function form( $instance ) {
      $tg_defaults['title'] = '';
      $tg_defaults['text'] = '';
      $tg_defaults['number'] = 4;
      $tg_defaults['type'] = 'latest';
      $tg_defaults['category'] = '';
      $instance = wp_parse_args( (array) $instance, $tg_defaults );
      $title = esc_attr( $instance[ 'title' ] );
      $text = esc_textarea($instance['text']);
      $number = $instance['number'];
      $type = $instance['type'];
      $category = $instance['category'];
	  
	  $image_url = 'bg_image_url';
	  $instance[ $image_url ] = esc_url( $instance[ $image_url ] );
      ?>
      
         <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'postGrid' ); ?></label>
         <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
      </p>
      <?php _e( 'Description','postGrid' ); ?>
      <textarea class="widefat" rows="5" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
      
      <p>
         <label for="<?php echo $this->get_field_id( $image_url ); ?>"> <?php _e( 'Background Image ', 'postGrid' ); ?></label>
         <div class="media-uploader" id="<?php echo $this->get_field_id( $image_url ); ?>">
            <div class="custom_media_preview">
               <?php if ( $instance[ $image_url ] != '' ) : ?>
                  <img class="custom_media_preview_default" src="<?php echo esc_url( $instance[ $image_url ] ); ?>" style="max-width:100%;" />
               <?php endif; ?>
            </div>
            <input type="text" class="widefat custom_media_input" id="<?php echo $this->get_field_id( $image_url ); ?>" name="<?php echo $this->get_field_name( $image_url ); ?>" value="<?php echo esc_url( $instance[$image_url] ); ?>" style="margin-top:5px;" />
            <button class="custom_media_upload button button-secondary button-large" id="<?php echo $this->get_field_id( $image_url ); ?>" data-choose="<?php esc_attr_e( 'Choose an image', 'postGrid' ); ?>" data-update="<?php esc_attr_e( 'Use image', 'postGrid' ); ?>" style="width:100%;margin-top:6px;margin-right:30px;"><?php esc_html_e( 'Select an Image', 'postGrid' ); ?></button>
         </div>
      </p>
      
      <p>
         <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of posts to display:', 'postGrid' ); ?></label>
         <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
      </p>

      <p><input type="radio" <?php checked($type, 'latest') ?> id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" value="latest"/><?php _e( 'Show latest Posts', 'postGrid' );?><br />
       <input type="radio" <?php checked($type,'category') ?> id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" value="category"/><?php _e( 'Show posts from a category', 'postGrid' );?><br /></p>

      <p>
         <label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Select category', 'postGrid' ); ?>:</label>
         <?php wp_dropdown_categories( array( 'show_option_none' =>' ','name' => $this->get_field_name( 'category' ), 'selected' => $category ) ); ?>
      </p>
      <?php
   }

   function update( $new_instance, $old_instance ) {
      $instance = $old_instance;
      $instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
      if ( current_user_can('unfiltered_html') )
         $instance['text'] =  $new_instance['text'];
      else
         $instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) );
      $instance[ 'number' ] = absint( $new_instance[ 'number' ] );
      $instance[ 'type' ] = $new_instance[ 'type' ];
      $instance[ 'category' ] = $new_instance[ 'category' ];
	  
	  $image_url = 'bg_image_url';
      $instance[ $image_url ] = esc_url_raw( $new_instance[ $image_url ] );

      return $instance;
   }

   function widget( $args, $instance ) {
      extract( $args );
      extract( $instance );

      global $post;
      $title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
      $text = isset( $instance[ 'text' ] ) ? $instance[ 'text' ] : '';
      $number = empty( $instance[ 'number' ] ) ? 4 : $instance[ 'number' ];
      $type = isset( $instance[ 'type' ] ) ? $instance[ 'type' ] : 'latest' ;
      $category = isset( $instance[ 'category' ] ) ? $instance[ 'category' ] : '';
	  //$image_url = isset( $instance[ 'image_url' ] ) ? $instance[ 'image_url' ] : '';
	  $image_url = 'bg_image_url';

      if( $type == 'latest' ) {
         $get_featured_posts = new WP_Query( array(
            'posts_per_page'        => $number,
            'post_type'             => 'post',
            'ignore_sticky_posts'   => true
         ) );
      }
      else {
         $get_featured_posts = new WP_Query( array(
            'posts_per_page'        => $number,
            'post_type'             => 'post',
            'category__in'          => $category
         ) );
      }
      echo $before_widget;
	  
	  $bgurl = '';
      ?>
      	<?php if ( $instance[ $image_url ] != '' ) :
		$bgurl = ' style="background-image:url('.esc_url( $instance[ $image_url ] ).')" '; // $instance[ $image_url ]; ?>
          <?php /*?><img class="custom_media_preview_default" src="<?php echo esc_url( $instance[ $image_url ] ); ?>" style="max-width:100%;" /><?php */?>
        <?php endif; ?>
      <?php
         if ( $type != 'latest' ) {
            /*$border_color = 'style="border-bottom-color:' . postGrid_category_color($category) . ';"';
            $title_color = 'style="background-color:' . postGrid_category_color($category) . ';"';*/
         } else {
            $border_color = '';
            $title_color = '';
         }
         if ( !empty( $title ) ) { echo '<h3 class="widget-title" '. $border_color .'><span ' . $title_color .'>'. esc_html( $title ) .'</span></h3>'; }
         if( !empty( $text ) ) { ?> <p> <?php echo esc_textarea( $text ); ?> </p> <?php } ?>
         <?php
		 echo '<div class="following-post-wrap" '.$bgurl.' >';
         $i=1;
         while( $get_featured_posts->have_posts() ):$get_featured_posts->the_post();
            ?>
            <?php if( $i == 0 ) { $featured = 'postGrid-featured-post-medium'; } else { $featured = 'postGrid-featured-post-small'; } ?>
            <?php if( $i == 0 ) { echo '<div class="first-post">'; } elseif ( $i == 1 ) { echo '<div class="following-post style1">'; } ?>
               <div class="single-article clearfix">
                  <?php
                  if( has_post_thumbnail() ) {
                     $image = '';
                     $title_attribute = get_the_title( $post->ID );
                     $image .= '<figure>';
                     $image .= '<a href="' . get_permalink() . '" title="'.the_title( '', '', false ).'">';
                     $image .= get_the_post_thumbnail( $post->ID, $featured, array( 'title' => esc_attr( $title_attribute ), 'alt' => esc_attr( $title_attribute ) ) ).'</a>';
                     $image .= '</figure>';
                     echo $image;
                  }
                  ?>
                  <div class="article-content">
                     <h3 class="entry-title">
                        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a>
                     </h3>
                     <div class="below-entry-meta">
                        <?php
                           $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
                           $time_string = sprintf( $time_string,
                              esc_attr( get_the_date( 'c' ) ),
                              esc_html( get_the_date() )
                           );
                           printf( __( '<span class="posted-on"><a href="%1$s" title="%2$s" rel="bookmark"><i class="fa fa-calendar-o"></i> %3$s</a></span>', 'postGrid' ),
                              esc_url( get_permalink() ),
                              esc_attr( get_the_time() ),
                              $time_string
                           );
                        ?>
                        <span class="byline"><span class="author vcard"><i class="fa fa-user"></i><a class="url fn n" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" title="<?php echo get_the_author(); ?>"><?php echo esc_html( get_the_author() ); ?></a></span></span>
                        <span class="comments"><i class="fa fa-comment"></i><?php comments_popup_link( '0', '1', '%' );?></span>
                     </div>
                     <?php if( $i == 0 ) { ?>
                     <div class="entry-content">
                        <?php the_excerpt(); ?>
                     </div>
                     <?php } ?>
                  </div>

               </div>
            <?php if( $i == 0 ) { echo '</div>'; } ?>
         <?php
            $i++;
         endwhile;
         if ( $i > 1 ) { echo '</div>'; }
         // Reset Post Data
         wp_reset_query();
         ?>
         </div>
      <!-- </div> -->
      <?php echo $after_widget;
      }
}

