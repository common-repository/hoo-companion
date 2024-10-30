<?php

 
// Creating the widget 
class HoocPosts_Widget extends WP_Widget {
 
function __construct() {
	parent::__construct(
	 
	'hooc_widget_posts', 
	 
	__('HC: Sidebar Posts', 'hoo-companion'), 
	 
	array( 'description' => __( 'Sidebar posts list.', 'hoo-companion' ), ) 
	);
}
 
// Creating widget front-end
 
public function widget( $args, $instance ) {
	
	$defaults = array( 'widget_title' => '', 'category' => '0', 'posts_count' => '8','taxonomy' =>'category', 'order_by' => 'date', 'style' => '0' );
	$instance = wp_parse_args( (array) $instance, $defaults );
	
	$cat   = esc_attr($instance['category']);
	$count = absint($instance['posts_count']);
	$widget_title = $instance['widget_title'];
	$order_by = $instance['order_by'];
	
	$html = '';
	
	$post_types = get_taxonomy( $instance['taxonomy'] )->object_type;
	
	//posts_type
	$query_args = array( 
			'post_type'      => $post_types,
			'posts_per_page'  => $count,
			'tax_query' => array(
				array(
					'taxonomy' => $instance['taxonomy'],
					'field'    => 'slug',
					'terms'    => array( $cat ),
				),
		)
	);
	
	$left_column  = '';
	$right_column = '';
	
	if( !$cat  ){
		
		$query_args = array( 
			'post_type'      => $post_types,
			'posts_per_page'  => $count,
		);
	}
	
	
	if( !in_array( $order_by, array('date', 'modified', 'rand') ) ){
		$query_args['orderby'] = 'meta_value_num';
		$query_args['meta_key'] = $order_by;
	}else{
		$query_args['orderby'] = $order_by;
	}

	$cats_post = new WP_Query( $query_args );		

	if( $cats_post->have_posts() ):
	
	$i = 0;
	
	
	while( $cats_post->have_posts()): $cats_post->the_post();
	 
	 $post_id   = get_the_ID();
	 $first_tag = HooCompanion::get_first_tag( get_the_ID() );
	 $first_tag = apply_filters('hoo_first_tag', $first_tag );
	 
	 $title = get_the_title();
	 $link = get_permalink();
	 $excerpt = get_the_excerpt();
	 
	 if( has_post_thumbnail() )
		$image = get_the_post_thumbnail( $post_id, 'govideo_thumbnail' );
	 else
		$image = HooCompanion::get_featured_image();
	
	if( !$image )
		$image = HooCompanion::get_featured_image();
	
	
			
	$small_post = '<article id="post-'.$post_id.'" class="'.join( ' ', get_post_class( 'small-post clearfix', $post_id ) ).'">
											<div class="wrap-vid">
												<div class="row">
												<div class="col-md-6">
												<div class="zoom-container">
													<div class="zoom-caption">
														<span>'.esc_attr($first_tag).'</span>
														<a href="'.esc_url($link).'">'.HooCompanion::get_zoom_icon(3).'</a>
														<p>'.esc_attr($title).'</p>
													</div>
													'.$image.'
												</div>
												</div>
												<div class="col-md-6">
												<div class="wrapper">
													<h5 class="vid-name entry-title"><a href="'.esc_url($link).'">'.esc_attr($title).'</a></h5>
													'.HooCompanion::posted_on().'
												</div>
												</div>
												</div>
											</div></article>';
											
		$small_post_2 = '<article id="post-'.$post_id.'" class="'.join( ' ', get_post_class( 'small-post clearfix', $post_id ) ).'">	
									<div class="wrap-vid">
										<div class="zoom-container">
											<div class="zoom-caption">
												<span>'.esc_attr($first_tag).'</span>
												<a href="'.esc_url($link).'">'.HooCompanion::get_zoom_icon(3).'</a>
												<p>'.esc_attr($title).'</p>
											</div>
											'.$image.'
										</div>
										<h3 class="vid-name entry-title"><a href="'.esc_url($link).'">'.esc_attr($title).'</a></h3>
										'.HooCompanion::posted_on().'
									</div>
								</article>';
		
		switch( $instance['style'] ){
		
		
		case "1":
			$html .= $small_post_2 ;
		break;
		default:
			$html .= $small_post ;
		break;
	}
	
	$i++;
	endwhile;

    wp_reset_postdata();
	endif;
	
	echo $args['before_widget'];
		if ( ! empty( $instance['widget_title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['widget_title'] ) . $args['after_title'];
		}
		
	echo $html;
	echo '<div class="line"></div>';	
	echo $args['after_widget'];

}
         
// Widget Backend 
public function form( $instance ) {
	
	$defaults = array( 'widget_title' => '', 'category' => '0', 'posts_count' => '8','taxonomy' =>'category', 'order_by' => 'date', 'style' => '0' );
	$instance = wp_parse_args( (array) $instance, $defaults );
	
	$args = array(
		'selected' => esc_attr($instance['category']),
		'name' => $this->get_field_name( 'category'  ),
		'id' => $this->get_field_id( 'category' ),
		'show_option_all' => __( 'All Categories', 'hoo-companion' ),
		'taxonomy' => $instance['taxonomy'],
		'class' => 'hoo-category',
		'value_field' => 'slug',
	);
	
	$uniqid = uniqid('hoo-select-taxonomy');
	
	?>
    <p>
        <label for="<?php echo $this->get_field_id( 'widget_title'  ); ?>"><?php _e('Widget Title', 'hoo-companion'); ?>:</label>
         <br />
		<input id="<?php echo $this->get_field_id( 'widget_title'  ); ?>" name="<?php echo $this->get_field_name( 'widget_title'  ); ?>" value="<?php echo esc_attr( $instance['widget_title']); ?>" class="widefat" /> 
        </p>
        
     
     <p>
	<label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e( 'Section Style', 'hoo-companion' ); ?>:</label> 
    <br />
     <select id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style'  ); ?>">
       
       <option   value="0" <?php selected( $instance['style'], '0' ,true ); ?>><?php _e( 'Style 1 (Title Right)', 'hoo-companion' ); ?></option>
       <option   value="1" <?php selected( $instance['style'], '1' ,true ); ?>><?php _e( 'Style 2 (Title Down)', 'hoo-companion' ); ?></option>
 
        
            </select>
            
	</p>
      
     <p>
	<label for="<?php echo $this->get_field_id( 'order_by' ); ?>"><?php _e( 'Order By', 'hoo-companion' ); ?>:</label> 
    <br />
     <select id="<?php echo $this->get_field_id( 'order_by' ); ?>" name="<?php echo $this->get_field_name( 'order_by'  ); ?>">
       
       <option   value="date" <?php selected( $instance['order_by'], 'date' ,true ); ?>><?php _e( 'Date', 'hoo-companion' ); ?></option>
       <option   value="modified" <?php selected( $instance['order_by'], 'modified' ,true ); ?>><?php _e( 'Modified', 'hoo-companion' ); ?></option>
       
       <option   value="rand" <?php selected( $instance['order_by'], 'rand' ,true ); ?>><?php _e( 'Random', 'hoo-companion' ); ?></option>
       
       <option   value="hoo_visits" <?php selected( $instance['order_by'], 'hoo_visits' ,true ); ?>><?php _e( 'Visits', 'hoo-companion' ); ?></option>
        
            </select>
            
	</p>
    
    
         <p>
	<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'Taxonomy', 'hoo-companion' ); ?>:</label> 
    <br />
     <select class="<?php echo $uniqid;?>" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy'  ); ?>">
     
     <?php
	
	$my_taxonomies = array( 'category'=>  __( 'Post Category', 'hoo-companion' ) );
	$taxonomies = get_taxonomies(array('public' => true, '_builtin' => false), 'objects');
	
	foreach( $taxonomies as $taxonomy ){
		$my_taxonomies[$taxonomy->name] = $taxonomy->label;
	}
	foreach( $my_taxonomies as $key => $value ){
	?>
           <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['taxonomy'], $key ,true ); ?>><?php echo esc_attr($value); ?></option>
     <?php }?>
     
     </select>
            
	</p>

	<p>
	<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Posts Category', 'hoo-companion' ); ?>:</label> 
    <br />
	 <?php wp_dropdown_categories( $args ); ?>
	</p>
    
    <p>
	<label for="<?php echo $this->get_field_id( 'posts_count' ); ?>"><?php _e( 'Posts Count', 'hoo-companion' ); ?>:</label> 
    <br />
     <select id="<?php echo $this->get_field_id( 'posts_count' ); ?>" name="<?php echo $this->get_field_name( 'posts_count'  ); ?>">
           <?php 
		
		   for( $i = 2; $i <=12; $i++ ){
			   ?>
           <option   value="<?php echo $i;?>" <?php selected( $instance['posts_count'], $i ,true ); ?>><?php echo $i;?></option>
           <?php }?>
            </select>
            
	</p>
    <script>
		
	jQuery(".<?php echo $uniqid;?>").change(function(){
		var obj = jQuery(this);

		var taxonomy = obj.val();
			jQuery.ajax({
				type: 'POST',
				url: '<?php echo esc_url(admin_url('admin-ajax.php'));?>',
				data:{action:'hoo_get_categories', taxonomy:taxonomy, name:'<?php echo $this->get_field_name( 'category' ); ?>', id:'<?php echo $this->get_field_id( 'category' ); ?>'},
				success: function ( data ) {
					
					
					obj.parents('.widget-content').find('.hoo-category').val(0).trigger('change');
					obj.parents('.widget-content').find('.hoo-category').html(data);
					if ( 'undefined' !== typeof wp && wp.customize )
						wp.customize.previewer.refresh();
					
				},
				complete: function(data){ }
			},'html'
		);

		});
	</script>
    
	<?php 
}
     
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
	
	$instance = $old_instance;

	$instance['widget_title'] = ( ! empty( $new_instance['widget_title'] ) ) ? esc_attr( $new_instance['widget_title'] ) : '';
	$instance['taxonomy'] = ( ! empty( $new_instance['taxonomy'] ) ) ? esc_attr( $new_instance['taxonomy'] ) : '';
	$instance['category'] = ( ! empty( $new_instance['category'] ) ) ? esc_attr( $new_instance['category'] ) : '0';
	$instance['posts_count'] = ( ! empty( $new_instance['posts_count'] ) ) ? absint( $new_instance['posts_count'] ) : '0';
	$instance['order_by'] = ( ! empty( $new_instance['order_by'] ) ) ? esc_attr( $new_instance['order_by'] ) : 'date';
	$instance['style'] = ( ! empty( $new_instance['style'] ) ) ? absint( $new_instance['style'] ) : '0';
	
	return $instance;
	}
} // Class wpb_widget ends here