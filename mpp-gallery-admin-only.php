<?php
/**
 *
 * Plugin Name: MediaPress Gallery Admin only
 * Description: This is small plugin for site admin that enables site admin to allow only admin can create gallery.
 * Author: BuddyDev
 *
 * @contributor: ravisharma
 */

// Exit if file accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mpp_Create_Gallery_Restriction
 */
class Mpp_Create_Gallery_Restriction {

	/**
	 * Class Instance
	 *
	 * @var Mpp_Create_Gallery_Restriction
	 */
	private static $instance = null;

	/**
	 * Mpp_Create_Gallery_Restriction constructor.
	 */
	private function __construct() {
		$this->setup();
	}

	/**
	 * Get instance
	 *
	 * @return Mpp_Create_Gallery_Restriction
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Setup callback for necessary hooks.
	 */
	private function setup() {
		add_action( 'mpp_admin_register_settings', array( $this, 'add_option' ) );
		add_filter( 'mpp_user_can_create_gallery', array( $this, 'modify_permission' ) );
	}

	/**
	 * Get WordPress roles
	 *
	 * @return array
	 */
	private function get_roles() {

		$roles      = get_editable_roles();
		$user_roles = array();

		foreach ( $roles as $role => $detail ) {
			$user_roles[ $role ] = $detail['name'];
		}

		return $user_roles;
	}

	/**
	 * Add option site admin can select who can create gallery
	 *
	 * @param MPP_Admin_Settings_Page $page Setting page.
	 */
	public function add_option( $page ) {

		$panel = $page->get_panel( 'addons' );

		$panel->add_section( 'mpp_admin_only', __( 'Gallery admin only settings', 'mpp-gallery-admin-only' ) )->add_field( array(
			'name'    => 'mppgao_admin_only',
			'label'   => __( 'Allow gallery creation for admin only', 'mpp-gallery-admin-only' ),
			'type'    => 'radio',
			'options' => array(
				1 => __( 'Yes', 'mpp-gallery-admin-only' ),
				0 => __( 'No', 'mpp-gallery-admin-only' ),
			),
			'default' => 0,
		) );
	}

	/**
	 * Modify gallery creation permissions
	 *
	 * @param bool $can_do Can create gallery or not.
	 *
	 * @return bool
	 */
	public function modify_permission( $can_do ) {

		$admin_only = mpp_get_option( 'mppgao_admin_only', 0 );

		if ( $admin_only && ! current_user_can( 'activate_plugins' ) ) {
			$can_do = false;
		}

		return $can_do;
	}
}

Mpp_Create_Gallery_Restriction::get_instance();
