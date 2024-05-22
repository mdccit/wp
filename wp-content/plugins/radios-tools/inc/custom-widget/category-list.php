<?php if (!defined('ABSPATH')) die('Direct access forbidden.');

class Radios_Category_List extends WP_Widget {


	function __construct() {
        $widget_opt = array(
            'classname'		 => 'radios-category-list',
            'description'	 => esc_html__('Radios Category List','radios-tools')
        );

        parent::__construct( 'Radios_Category_List', esc_html__( 'Radios Category List', 'radios-tools' ), $widget_opt );
    }

	
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		$va_category_HTML ='<div class="category-widget">';
		if ( ! empty( $instance['radios_title'] ) && !$instance['radios_hide_title']) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['radios_title'] ) . $args['after_title'];
		}
		
		if(isset($instance['radios_taxonomy_type'])){
				$args_val = array( 'hide_empty' => 'true', 'parent'=>0 );				
				$excludeCat= $instance['radios_selected_categories'] ? $instance['radios_selected_categories'] : '';
				$radios_action_on_cat= $instance['radios_action_on_cat'] ? $instance['radios_action_on_cat'] : '';
				if($excludeCat && $radios_action_on_cat!='')
				$args_val[$radios_action_on_cat] = $excludeCat;
				
				$terms = get_terms( $instance['radios_taxonomy_type'], $args_val );
				
				if ( !empty($terms) ) {	

					foreach ( $terms as $term ) {
						$term_link = get_term_link( $term );
						
						if ( is_wp_error( $term_link ) ) {
						continue;
						}
						
					$carrentActiveClass='';	
					$category_image = '';
					if($term->taxonomy=='category' && is_category())
					{
                 $thisCat = get_category(get_query_var('cat'),false);
              
                
					  if($thisCat->term_id == $term->term_id)
						$carrentActiveClass='class="active-cat img_cat_item_list_Single"';
				    }
					 
					if(is_tax())
					{
					    $currentTermType = get_query_var( 'taxonomy' );
					    $termId= get_queried_object()->term_id;
						 if(is_tax($currentTermType) && $termId==$term->term_id)
                    $carrentActiveClass='class="active-cat img_cat_item_list_Single"';
                    
					}
                  

				$meta = get_term_meta($term->term_id, 'radios_cate_meta', true);

				$icon_name = !empty( $meta['icon_name'] )? $meta['icon_name'] : '';
				 
						$va_category_HTML .='<a href="' . esc_url( $term_link ) . '"><div class="category-list-item d-flex position-relative align-items-center">
                        <div class="inner-icon d-flex  align-items-center justify-content-center"><i class="'.esc_attr($icon_name).'"></i></div>
                        <div class="inner-text">
                            '. $term->name.' ';
                            if (empty( $instance['radios_hide_count'] )) {
                                $va_category_HTML .='<span class="cat-number">('.$term->count.')</span>';
                            }
                            $va_category_HTML .='
                        </div>
                        ';
						
                        $va_category_HTML .='
                        </div></a>
                        ';
                        
					}
				}
			
			}	
		$va_category_HTML .='</div>';

		echo wp_kses_post($va_category_HTML);
		echo $args['after_widget'];
	}


	public function form( $instance ) {
		$radios_title 				= ! empty( $instance['radios_title'] ) ? $instance['radios_title'] : esc_html__( 'WP Categories', 'radios-tools' );
		$radios_hide_title 			= ! empty( $instance['radios_hide_title'] ) ? $instance['radios_hide_title'] : '';
		$radios_taxonomy_type 			= ! empty( $instance['radios_taxonomy_type'] ) ? $instance['radios_taxonomy_type'] : esc_html__( 'category', 'radios-tools' );
		$radios_selected_categories 	= (! empty( $instance['radios_selected_categories'] ) && ! empty( $instance['radios_action_on_cat'] ) ) ? $instance['radios_selected_categories'] : array();
		$radios_action_on_cat 			= ! empty( $instance['radios_action_on_cat'] ) ? $instance['radios_action_on_cat'] : '';
		$radios_hide_count 			= ! empty( $instance['radios_hide_count'] ) ? $instance['radios_hide_count'] : '';
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'radios_title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'radios_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'radios_title' ) ); ?>" type="text" value="<?php echo esc_attr( $radios_title ); ?>">
		</p>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'radios_hide_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'radios_hide_title' ) ); ?>" type="checkbox" value="1" <?php checked( $radios_hide_title, 1 ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id( 'radios_hide_title' ) ); ?>"><?php _e( esc_attr( 'Hide Title' ) ); ?> </label> 
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'radios_taxonomy_type' ) ); ?>"><?php _e( esc_attr( 'Taxonomy Type:' ) ); ?></label> 
		<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'radios_taxonomy_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'radios_taxonomy_type' ) ); ?>">
					<?php 
					$args = array(
					  'public'   => true,
					  '_builtin' => false
					  
					); 
					$output = 'names'; // or objects
					$operator = 'and'; // 'and' or 'or'
					$taxonomies = get_taxonomies( $args, $output, $operator ); 
					array_push($taxonomies,'category');
					if ( !empty($taxonomies) ) {
					foreach ( $taxonomies as $taxonomy ) {

						echo '<option value="'.$taxonomy.'" '.selected($taxonomy,$radios_taxonomy_type).'>'.$taxonomy.'</option>';
					}
					}

				?>    
		</select>
		</p>
		<p>
		<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'radios_action_on_cat' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'radios_action_on_cat' ) ); ?>">
           <option value="" <?php selected($radios_action_on_cat,'' )?> > <?php echo esc_html__('Show All Category','radios-tools').' :'; ?> </option>       
           <option value="include" <?php selected($radios_action_on_cat,'include' )?> > <?php echo esc_html__("Include Selected Category:","radios-tools"); ?> </option>       
           <option value="exclude" <?php selected($radios_action_on_cat,'exclude' )?> > <?php echo esc_html__("Exclude Selected Category","radios-tools").' :'; ?> </option>
		</select> 
		<select class="widefat radios-category-widget" id="<?php echo esc_attr( $this->get_field_id( 'radios_selected_categories' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'radios_selected_categories' ) ); ?>[]" multiple>
					<?php 			
					if($radios_taxonomy_type){
					$args = array( 'hide_empty=0' );
					$terms = get_terms( $radios_taxonomy_type, $args );
			        echo '<option value="" '.selected(true, in_array('',$radios_selected_categories), false).'>'.esc_html('None ','radios-tools').'</option>';
					if ( !empty($terms) ) {
					foreach ( $terms as $term ) {
						echo '<option value="'.$term->term_id.'" '.selected(true, in_array($term->term_id,$radios_selected_categories), false).'>'.$term->name.'</option>';
					}
				    	
					}
				}

				?>    
		</select>
		</p>
		<p>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'radios_hide_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'radios_hide_count' ) ); ?>" type="checkbox" value="1" <?php checked( $radios_hide_count, 1 ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id( 'radios_hide_count' ) ); ?>"><?php echo esc_attr__( 'Hide Count','radios-tools' ) ; ?> </label> 
		</p>
		<?php 
	}

	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['radios_title'] 					= ( ! empty( $new_instance['radios_title'] ) ) ? strip_tags( $new_instance['radios_title'] ) : '';
		$instance['radios_hide_title'] 			= ( ! empty( $new_instance['radios_hide_title'] ) ) ? strip_tags( $new_instance['radios_hide_title'] ) : '';
		$instance['radios_taxonomy_type'] 			= ( ! empty( $new_instance['radios_taxonomy_type'] ) ) ? strip_tags( $new_instance['radios_taxonomy_type'] ) : '';
		$instance['radios_selected_categories'] 	= ( ! empty( $new_instance['radios_selected_categories'] ) ) ? $new_instance['radios_selected_categories'] : '';
		$instance['radios_action_on_cat'] 			= ( ! empty( $new_instance['radios_action_on_cat'] ) ) ? $new_instance['radios_action_on_cat'] : '';
		$instance['radios_hide_count'] 			= ( ! empty( $new_instance['radios_hide_count'] ) ) ? strip_tags( $new_instance['radios_hide_count'] ) : '';
		return $instance;
	}
}


