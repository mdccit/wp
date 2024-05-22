<?php

function radios_post_author_avatars($size) {
  echo get_avatar(get_the_author_meta('email'), $size);
}

add_action('genesis_entry_header', 'radios_post_author_avatars');

function radios_product_category (){
  $terms = get_terms( array(
      'taxonomy'    => 'product_cat',
      'hide_empty'  => true,
  ) );

  $cat_list = [];
  foreach($terms as $post) {
    $cat_list[$post->slug]  = [$post->name];
  }
  return $cat_list;
}


if ( ! function_exists( 'radios_item_tag_lists' ) ) {
  function radios_item_tag_lists(  $type = '', $query_args = array() ) {

    $options = array();

    switch( $type ) {

      case 'pages':
      case 'page':
      $pages = get_pages( $query_args );

      if ( !empty($pages) ) {
        foreach ( $pages as $page ) {
          $options[$page->post_title] = $page->ID;
        }
      }
      break;

      case 'posts':
      case 'post':
      $posts = get_posts( $query_args );

      if ( !empty($posts) ) {
        foreach ( $posts as $post ) {
          $options[$post->post_title] = lcfirst($post->ID);
        }
      }
      break;

      case 'tags':
      case 'tag':

      if (isset($query_args['taxonomies']) && taxonomy_exists($query_args['taxonomies'])) {
        $tags = get_terms( $query_args['taxonomies'] );
          if ( !is_wp_error($tags) && !empty($tags) ) {
            foreach ( $tags as $tag ) {
              $options[$tag->name] = $tag->term_id;
          }
        }
      }
      break;
    }

    return $options;

  }
}


/**
 * Post Time Ago
 */
function radios_ready_time_ago(){
  return human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) );
}

/**
 * Category Item
 *
 * @return loop
 */
function radios_category_pl(){
  $catgorys = get_the_category();
  foreach( $catgorys as $key => $category):
      ?>
      <a class="cat" href="<?php echo esc_url(get_category_link($category->term_id)); ?>">
        <?php echo esc_html($category->cat_name); ?>
      </a>
  <?php endforeach;
}



/**
 * Post Social Share
 *
 * @return void
 */
function radios_post_share() {

  $permalink = get_permalink( get_the_ID() );
  $title     = get_the_title();
?>

<h5 class="title"><?php esc_html_e( 'Share:', 'radios-tools' );?></h5>
<ul class="ul_li">
  <li><a class="fb" onClick="window.open('http://www.facebook.com/sharer.php?u=<?php echo esc_url( $permalink ); ?>','Facebook','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;" href="http://www.facebook.com/sharer.php?u=<?php echo esc_url( $permalink ); ?>"><i class="fab fa-facebook-f"></i></a></li>

  <li><a class="tw" onClick="window.open('http://twitter.com/share?url=<?php echo esc_url( $permalink ); ?>&amp;text=<?php echo esc_attr( $title ); ?>','Twitter share','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;" href="http://twitter.com/share?url=<?php echo esc_url( $permalink ); ?>&amp;text=<?php echo str_replace( " ", "%20", $title ); ?>"><i class="fab fa-twitter"></i></a></li>

  <li><a class="ln" onClick="window.open('https://www.linkedin.com/cws/share?url=<?php echo esc_url( $permalink ); ?>&amp;text=<?php echo esc_attr( $title ); ?>','Linkedin share','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;" href="http://twitter.com/share?url=<?php echo esc_url( $permalink ); ?>&amp;text=<?php echo str_replace( " ", "%20", $title ); ?>"><i class="fab fa-linkedin-in"></i></a></li>

  <li><a class="pt" href='javascript:void((function()%7Bvar%20e=document.createElement(&apos;script&apos;);e.setAttribute(&apos;type&apos;,&apos;text/javascript&apos;);e.setAttribute(&apos;charset&apos;,&apos;UTF-8&apos;);e.setAttribute(&apos;src&apos;,&apos;http://assets.pinterest.com/js/pinmarklet.js?r=&apos;+Math.random()*99999999);document.body.appendChild(e)%7D)());'><i class="fab fa-pinterest-p"></i></a></li>

</ul>
<?php 
}