<?php if( !defined('ABSPATH') ) die();

require_once('class_reservationshelpers.php');

class Reservations extends ReservationsHelpers {
	public function __construct() {
		add_action( 'init', array( $this, 'wpInit' ) );
		
		add_action( 'save_post', array( $this, 'savePost' ) );
			
		add_action( 'add_meta_boxes_'. $this->getCustomPostName(), array( $this, 'finalizeBoxes') );
		add_action( 'manage_'. $this->getCustomPostName() .'_posts_custom_column', array( $this, 'managePostsCustomColumn' ), 10, 2 );

		add_filter( 'manage_'. $this->getCustomPostName() .'_posts_columns', array( $this, 'managePostsColumns') );
		add_filter( 'manage_edit-'. $this->getCustomPostName() .'_sortable_columns', array( $this, 'managePostsColumns') );
		
		add_filter( 'post_row_actions', array( $this, 'postRowActions' ), 10, 2 );
		add_filter( 'bulk_actions-edit-'. $this->getCustomPostName(), array( $this, 'removeBulkActions' ) );
		
		register_activation_hook( __FILE__, array( $this, 'wpReInit' ) );
	}
	
	public function addBox( $box_name, $box_id, $box_type ) {
		$this->insertBasePostColumns( $box_name, $box_type, $box_id );
		$this->insertPostColumns( $box_id, $box_name );
	}
	
	public function finalizeBoxes() {
		$meta_keys = get_post_custom( $post->ID );
		
		foreach( $this->getBasePostColumns() as $box ) {
			$box_name = $box[ 'name' ];
			$box_id = 'kdev_box_' . $box[ 'name' ];
				
			add_meta_box( $box_id , __( $box_name ), function() use( $box, $meta_keys ) {
				wp_nonce_field( plugin_basename( __FILE__ ), $this->getCustomPostName() );
				
				$checked = '';
				$boxValue = $meta_keys[ $box[ 'id' ] ][0] ?? '';
				
				if( $box[ 'type' ] === 'checkbox' ) {
					$checked = checked( $boxValue, true, false );
					$boxValue = 1;
				}
				
				echo sprintf( '<input type="%s" name="reservations[%s]" value="%s" %s>', $box[ 'type' ], $box[ 'id' ], $boxValue, $checked );
			}, $this->getCustomPostName(), 'normal', 'high');
		}
	}
	
	public function savePost() {
		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
        if( !wp_verify_nonce( $_POST[ $this->getCustomPostName() ], plugin_basename(__FILE__) ) ) return;

		global $post;

        if( !isset( $_POST[ 'reservations' ] ) || !isset( $post->ID ) || get_post_type( $post->ID ) !== $this->getCustomPostName() ) return;
		
		foreach( $this->getBasePostColumns() as $box ) {
			update_post_meta( $post->ID, $box[ 'id' ], $_POST[ 'reservations' ][ $box[ 'id' ] ] ?? 0 );
		}
	}
	
	public function managePostsCustomColumn( $column_name, $post_id ) {
		$postColumns = $this->getPostColumns();
		$baseColumns = $this->getBasePostColumns();
		
		$column = $postColumns[ $column_name ];
		$columnId = $baseColumns[ $column ][ 'id' ];
		$columnType = $baseColumns[ $column ][ 'type' ];
		
		$postValue = get_post_meta( $post_id, $columnId, true );
		
		if( $columnType === 'checkbox' ) {
			$postValue = $postValue ? 'true' : 'false';
		}
		
		echo $postValue;
	}
	
	public function managePostsColumns( $defaults ) {
		return $this->getPostColumns();
	}
	
	public function removeBulkActions( $actions ) {
        return array();
	}
	
	public function postRowActions( $actions, $post ) {
		if ( $this->getCustomPostName() === $post->post_type ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		
		return $actions;
	}
}
