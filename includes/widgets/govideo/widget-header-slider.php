<?php

// Creating the widget 
class HoocHeaderSlider_Widget extends WP_Widget {
 
function __construct() {
	parent::__construct(
	 
	'hooc_header_slider', 
	 
	__('HC: Section Header Slider', 'hoo-companion'), 
	 
	array( 'description' => __( 'Slider below main menu.', 'hoo-companion' ), ) 
	);
}
 
// Creating widget front-end
 
public function widget( $args, $instance ) {
	
	$defaults = array( 'category' => '', 'posts_count' => '8', 'taxonomy' =>'category' );
	$instance = wp_parse_args( (array) $instance, $defaults );
	
	$cat   = esc_attr($instance['category']);
	$count = absint($instance['posts_count']);

	$post_types = get_taxonomy( $instance['taxonomy'] )->object_type;

	//posts_type
	$query_args = array( 
			//'category__in'   => array( $cat ),
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
	
	if( !$cat  ){
		
		$query_args = array( 
			'post_type'      => $post_types,
			'posts_per_page'  => $count,
		);
	}
	
	$related_cats_post = new WP_Query( $query_args );

	if($related_cats_post->have_posts()):
	 while($related_cats_post->have_posts()): $related_cats_post->the_post();
	 
	 $first_tag = HooCompanion::get_first_tag( get_the_ID() );
	 $first_tag = apply_filters('hoo_first_tag', $first_tag );
	
	$css_class = 'item';

	?>
		<div class="<?php echo esc_attr($css_class); ?>">
			<div class="zoom-container">
				<div class="zoom-caption">
					<span><?php echo esc_attr($first_tag); ?></span>
					<a href="<?php the_permalink(); ?>">
						<?php echo HooCompanion::get_zoom_icon(3);?>
					</a>
					<p><?php the_title(); ?></p>
				</div>
				<?php
				if( has_post_thumbnail() ){
					//the_post_thumbnail( 'govideo_thumbnail' );
					$thumbnail = get_the_post_thumbnail( null, 'govideo_thumbnail' );
					
					if( $thumbnail == '' )
						echo HooCompanion::get_featured_image();
					else
						echo $thumbnail;
				}
				else
					echo HooCompanion::get_featured_image();
				?>
			</div>
		</div>
<?php endwhile;

    wp_reset_postdata();
     endif;

}
         
// Widget Backend 
public function form( $instance ) {
	
	$defaults = array( 'category' => '', 'posts_count' => '8', 'taxonomy' =>'category' );
	$instance = wp_parse_args( (array) $instance, $defaults );
	
	$args = array(
		'selected' => esc_attr($instance['category']),
		'name' => $this->get_field_name( 'category'  ),
		'id' => $this->get_field_id( 'category' ),
		'show_option_all' => __( 'All Categories', 'hoo-companion' ),
		//'taxonomy' => $instance['taxonomy'],
		'class' => 'hoo-category',
		'value_field' => 'slug',
		'hide_if_empty' => false
	);
	
	$uniqid = uniqid('hoo-select-taxonomy');
	
	?>
    
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
	<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Header Slider Posts', 'hoo-companion' ); ?>:</label> 
    <br />
	 <?php wp_dropdown_categories( $args ); ?>
	</p>
    
    <p>
	<label for="<?php echo $this->get_field_id( 'posts_count' ); ?>"><?php _e( 'Slider Posts Count', 'hoo-companion' ); ?>:</label> 
    <br />
     <select id="<?php echo $this->get_field_id( 'posts_count' ); ?>" name="<?php echo $this->get_field_name( 'posts_count'  ); ?>">
           <?php 
		
		   for( $i = 5; $i <=20; $i++ ){
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
	$instance['taxonomy'] = ( ! empty( $new_instance['taxonomy'] ) ) ? esc_attr( $new_instance['taxonomy'] ) : '';
	$instance['category'] = ( ! empty( $new_instance['category'] ) ) ? esc_attr( $new_instance['category'] ) : '';
	$instance['posts_count'] = ( ! empty( $new_instance['posts_count'] ) ) ? absint( $new_instance['posts_count'] ) : '8';
	
	return $instance;
	}
} // Class wpb_widget ends here