<?php if( !defined('ABSPATH') ) die();

class ReservationsHelpers {
	protected $custom_post_name = 'reservation';
	protected $display_post_columns = array();
	protected $base_post_columns = array();
	
	public function wpInit() {
		register_post_type( $this->getCustomPostName() ,
			array(
				'labels' => array(
					'name' 					=> __( 'Reservations' ),
					'singular_name' 		=> __( 'Reservations' ),
					'add_new'				=> __( 'Add New' ),
					'add_new_item'        	=> __( 'Add New Reservation' ),
					'edit_item'           	=> __( 'Edit Reservation' ),
					'new_item'            	=> __( 'New Reservation' ),
					'all_items'           	=> __( 'All Reservations' ),
					'view_item'           	=> __( 'View Reservation' ),
					'search_items'        	=> __( 'Search Reservations' ),
					'not_found'           	=> __( 'No events found' ),
					'not_found_in_trash'	=> __( 'No events found in Trash' ),
					'menu_name'       		=> __( 'Reservations' ),
				),
				'capabilities' => array(
					'edit_post'          => 'edit_reservations', 
					'read_post'          => 'read_reservations', 
					'delete_post'        => 'remove_reservations', 
					'edit_posts'         => 'edit_reservations', 
					'edit_others_posts'  => 'remove_reservations', 
					'publish_posts'      => 'add_reservations',       
					'read_private_posts' => 'remove_reservations', 
					'create_posts'       => 'add_reservations', 
				),
				'map_meta_cap'		=> false,
				'capability_type'  	=> 'post',
				'supports' 			=> array( 'custom-fields', 'revisions',  ),
				'public' 			=> false,
				'query_var'    		=> true,
				'has_archive' 		=> false,
				'rewrite' 			=> array( 'slug' => $this->getCustomPostName() ),
				'show_in_rest' 		=> false,
				'show_ui'        	=> true,
				'show_in_menu' 		=> true,
				'show_in_nav_menus' => true,
				'show_in_admin_bar' => true,
				'show_in_quick_edit' => false
			)
		);
		
		add_role( 'Manager', __( 'Manager' ),
			array(
			   'read'  => true,
			   'read_reservations'  => true,
			   'add_reservations'	=> true,
			   'edit_reservations'	=> true
			)
		);
		
		add_role( 'Moderator', __( 'Moderator' ),
			array(
				'read'  => true,
				'read_reservations'  => true,
				'add_reservations'	=> true,
				'edit_reservations'	=> true,
				'remove_reservations' => true
			)
		);
		
		$adminRole = get_role( 'administrator' );
		$adminRole->add_cap( 'read_reservations', true );
		$adminRole->add_cap( 'add_reservations', true );
		$adminRole->add_cap( 'edit_reservations', true );
		$adminRole->add_cap( 'remove_reservations', true );
	}
	
	public function wpReInit() {
		$this->wpInit();
		flush_rewrite_rules();
	}
	
	protected function insertPostColumns( $key, $value ) {
		$this->display_post_columns[ $key ] = $value;
	}
	
	protected function insertBasePostColumns( $box_name, $box_type, $box_id ) {
		$this->base_post_columns[ $box_name ] = array( 
			'name' => $box_name, 
			'type' => $box_type,
			'id' => 'kdev_' . $box_id
		);
	}
	
	protected function getCustomPostName() {
		return $this->custom_post_name;
	}
	
	protected function getBasePostColumns() {
		return $this->base_post_columns;
	}
	
	protected function getPostColumns() {
		return $this->display_post_columns;
	}
}