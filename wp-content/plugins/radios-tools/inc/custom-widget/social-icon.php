<?php
/**
 * Get Social Icon
 * 
 */
function radios_get_social_icon(){ 
	$mag_global_social   = cs_get_option('radios-social-global');
	?>
	<ul class="widget__social">
		<?php foreach($mag_global_social as $icons):?>
		<li>
			<div class="left-text ul_li">
				<span class="icon"><i class="<?php echo esc_html($icons['social_icon']);?>"></i></span>
				<?php if(!empty($icons['social_title'])):?>
					<span><?php echo esc_html($icons['social_title']);?></span>
				<?php endif;?>
			</div>
			<?php if(!empty($icons['social_follow_title'])):?>
				<a href="<?php echo esc_url($icons['social_link']);?>"><?php echo esc_html($icons['social_follow_title']);?></a>
			<?php endif;?>
		</li>
		<?php endforeach;?>     
	</ul>
<?php }
//Social Widget
class Radios_Social_Icons extends WP_Widget
{
	
	/** constructor */
	function __construct()
	{
		parent::__construct( /* Base ID */'Radios_Social_Icons', /* Name */esc_html__('radios Social Icons','aginco'), array( 'description' => esc_html__('Show the radios Social Icons', 'aginco' )) );
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance)
	{
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		
		echo wp_kses_post($before_widget);?>
      		
            <!-- Sidebar Widget / Social Widget -->
            <div class="sidebar-social-widget">
                <div class="widget-content">
                    <!-- Sidebar Title -->
                    <?php echo wp_kses_post($before_title.$title.$after_title); ?>
                    
                    <!-- Social Box -->                    
                    <?php if( $instance['show'] ): ?>
                    <?php echo wp_kses_post(radios_get_social_icon()); ?>
                    <?php endif; ?> 
                    
                </div>
            </div>
            
        <?php
		
		echo wp_kses_post($after_widget);
	}
	
	
	/** @see WP_Widget::update */
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show'] = $new_instance['show'];
		
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance)
	{
		$title = ($instance) ? esc_attr($instance['title']) : esc_html__('Social Icons', 'aginco');
		$show = ($instance) ? esc_attr($instance['show']) : '';
		
		?>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title: ', 'aginco'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('show')); ?>"><?php esc_html_e('Show Social Icons:', 'aginco'); ?></label>
			<?php $selected = ( $show ) ? ' checked="checked"' : ''; ?>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('show')); ?>"<?php echo esc_attr($selected); ?> name="<?php echo esc_attr($this->get_field_name('show')); ?>" type="checkbox" value="true" />
        </p>      
                
		<?php 
	}
	
}