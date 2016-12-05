<?php
/**
 * Plugin Name: Darwin Image
 * Plugin URI: 
 * Description: It is a plugin to show before and after image.
 * Author: Obi Plabon
 * Author URI: http://obiplabon.im/
 * Version: 1.0.0
 * Text Domain: darwin-image
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DARWIN_IMAGE_VER', '1.0.0' );
define( 'DARWIN_IMAGE_URI', plugin_dir_url( __FILE__ ) );


class Darwin_Image {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_assets') );
		add_shortcode( 'darwin_image', array($this, 'render') );
	}

	/**
	 * Add js and css dependencies.
	 * @return void
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'twentytwenty',
			DARWIN_IMAGE_URI . 'assets/css/twentytwenty.css',
			array(),
			DARWIN_IMAGE_VER
			);

		wp_enqueue_script(
			'jquery-event-move',
			DARWIN_IMAGE_URI . 'assets/js/jquery.event.move.js',
			array('jquery'),
			DARWIN_IMAGE_VER,
			true
			);

		wp_enqueue_script(
			'jquery-twentytwenty',
			DARWIN_IMAGE_URI . 'assets/js/jquery.twentytwenty.js',
			array('jquery'),
			DARWIN_IMAGE_VER,
			true
			);

		wp_add_inline_script(
			'jquery-twentytwenty',
			';(function($){ $(".darwin-image").twentytwenty({default_offset_pct: 0.7}); }(jQuery))'
			);
	}

	/**
	 * Render shortcode html output.
	 * @param  array  $atts    Shortcode attributes.
	 * @param  string $content 
	 * @return string          Shortcode output.
	 */
	public function render( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'type'        => 'id',
			'before'      => 0,
			'after'       => 0,
			'size'        => 'medium',
			'orientation' => 'horizontal' // Only two available - horizontal & vertical
			), $atts );

		if ( 'id' === $atts['type'] ) {
			$atts['before'] = wp_get_attachment_image_url( absint( $atts['before'] ), $atts['size'] );
			$atts['after'] = wp_get_attachment_image_url( absint( $atts['after'] ), $atts['size'] );
		}

		return sprintf(
			'<div class="darwin-image" data-orientation="%s">'
				.'<img src="%s" alt="%s">'
				.'<img src="%s" alt="%s">'
			.'</div>',
			esc_attr( $atts['orientation'] ),
			esc_url( $atts['before'] ),
			esc_attr_x( 'Before', 'Darwin Before Image', 'darwin-image' ),
			esc_url( $atts['after'] ),
			esc_attr_x( 'After', 'Darwin After Image', 'darwin-image' )
		);
	}

}

new Darwin_Image;
