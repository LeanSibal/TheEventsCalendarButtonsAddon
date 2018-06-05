<?php
/**
Plugin Name: The Events Calendar Custom Button Add-on
Plugin URI: https://renesejling.dk/
Description: Custom button add-on for events calendar before and after the description
Version: 1.0.0
Author: René Sejling
Author URI: https://renesejling.dk
*/

class TheEventsCalendarCustomButtonAddOn {

	public $version = '1.0.0';


	public function __construct(){
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action('tribe_events_single_event_before_the_content', [ $this, 'add_button_to_tribe_events' ] );
		add_action('tribe_events_single_event_after_the_content', [ $this, 'add_button_to_tribe_events' ] );
		add_action('add_meta_boxes', [ $this, 'tribe_events_meta_box' ] );
		add_action('save_post', [ $this, 'save_tribe_events_meta_box_data' ] );
	}

	public function enqueue_scripts() {
		wp_register_style(
			'tribe-events-buttons',
			plugins_url( 'the-events-calendar-buttons-addon/css/button.css' ),
			[],
			$this->version
		);
	}

	public function tribe_events_meta_box() {
		add_meta_box(
			'tribe-events-buttons',
			'Booking Buttons',
			[ $this, 'tribe_events_meta_box_callback' ],
			'tribe_events',
			'side'
		);
	}

	public function tribe_events_meta_box_callback( $post ) {
		ob_start();
		wp_nonce_field( 'tribe_events_buttons_addon_nonce', 'tribe_events_buttons_addon_nonce' );
		$button_top_active = get_post_meta( $post->ID, '_button_top_active', true );
		$button_top_name = get_post_meta( $post->ID, '_button_top_name', true );
		$button_top_link = get_post_meta( $post->ID, '_button_top_link', true );
		$button_bottom_active = get_post_meta( $post->ID, '_button_bottom_active', true );
		$button_bottom_name = get_post_meta( $post->ID, '_button_bottom_name', true );
		$button_bottom_link = get_post_meta( $post->ID, '_button_bottom_link', true );
		?>
<div class="eventForm" style="padding-top:20px">
	<label>
	<input type="checkbox" name="button_top_active" <?php echo ( !empty( $button_top_active ) && $button_top_active == 'on' ) ? 'checked' : ''; ?>> Show Button at the Top
	</label>
	<table>
		<tr>
			<td>Button Text</td>
			<td><input type="text" placeholder="Book Here" name="button_top_name" value="<?php echo !empty( $button_top_name ) ? $button_top_name : ''; ?>"></td>
		</tr>
		<tr>
			<td>Button Link</td>
			<td><input type="text" placeholder="http://www.sundhedsfabrikken.dk/" name="button_top_link" value="<?php echo !empty( $button_top_link ) ? $button_top_link : ''; ?>"></td>
		</tr>
	</table>
	<div style="margin-top:30px">
		<label>
			<input type="checkbox" name="button_bottom_active" <?php echo ( !empty( $button_bottom_active ) && $button_top_active == 'on' ) ? 'checked' : ''; ?>> Show Button at the bottom
		</label>
		<table>
			<tr>
				<td>Button Text</td>
				<td><input type="text" placeholder="Book Here" name="button_bottom_name" value="<?php echo !empty( $button_bottom_name ) ? $button_bottom_name : ''; ?>"></td>
			</tr>
			<tr>
				<td>Button Link</td>
				<td><input type="text" placeholder="http://www.sundhedsfabrikken.dk/" name="button_bottom_link" value="<?php echo !empty( $button_bottom_link ) ? $button_bottom_link : ''; ?>"></td>
			</tr>
		</table>
	</div>
</div>
		<?php
		echo ob_get_clean();
	}

	public function add_button_to_tribe_events() {
		global $post;
		$prefix = ( current_action() == 'tribe_events_single_event_before_the_content' ) ? '_button_top_' : '_button_bottom_';
		if( get_post_meta( $post->ID, $prefix . 'active', true ) !== 'on' ) return;
		$text = get_post_meta( $post->ID, $prefix . 'name', true );
		$link = get_post_meta( $post->ID, $prefix . 'link', true );
		if( empty( $text ) || empty( $link ) ) return false;
		wp_enqueue_style('tribe-events-buttons');
		ob_start();
		?>
		<div class="tribe-events-buttons-addon-container">
			<a href="<?php echo $link; ?>" class="tribe-events-buttons-addon" ><?php echo $text; ?></a>
		</div>
		<?php
		echo ob_get_clean();
	}

	public function save_tribe_events_meta_box_data( $post_id ) {
		if( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
		if( ! wp_verify_nonce( $_POST['tribe_events_buttons_addon_nonce'], 'tribe_events_buttons_addon_nonce' ) ) return $post_id;
		if( empty( $_POST['post_type'] ) || $_POST['post_type'] !== 'tribe_events' ) return $post_id;
		if( !empty( $_POST['button_top_active'] ) ) {
			update_post_meta( $post_id, '_button_top_active', $_POST['button_top_active'] );
		}
		if( !empty( $_POST['button_top_name'] ) ) {
			update_post_meta( $post_id, '_button_top_name', $_POST['button_top_name'] );
		}
		if( !empty( $_POST['button_top_link'] ) ) {
			update_post_meta( $post_id, '_button_top_link', $_POST['button_top_link'] );
		}
		if( !empty( $_POST['button_bottom_active'] ) ) {
			update_post_meta( $post_id, '_button_bottom_active', $_POST['button_bottom_active'] );
		}
		if( !empty( $_POST['button_bottom_name'] ) ) {
			update_post_meta( $post_id, '_button_bottom_name', $_POST['button_bottom_name'] );
		}
		if( !empty( $_POST['button_bottom_link'] ) ) {
			update_post_meta( $post_id, '_button_bottom_link', $_POST['button_bottom_link'] );
		}
	}
}

$theEventsCalendarCustomButtonAddOn = new TheEventsCalendarCustomButtonAddOn();
