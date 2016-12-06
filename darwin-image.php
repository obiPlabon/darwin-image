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

	protected $tag = 'darwin_image';

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_assets') );
		add_action( 'vc_before_init', array($this, 'vc_inject') );

		add_shortcode( $this->tag, array($this, 'render') );
	}

	protected function get_image_sizes() {
		$sizes = get_intermediate_image_sizes();
		$map = array();
		for ( $i = 0, $len = count($sizes); $i < $len; $i++ ) {
			$map[ucwords( str_replace( array('-', '_'), ' ', $sizes[$i] ) )] = $sizes[$i];
		}
		return $map;
	}

	public function vc_inject() {
		vc_map( array(
			'name' => esc_html__( 'Darwin Image', 'darwin-image' ),
			'description' => esc_html__( 'Easily visualize the transition between two image.', 'darwin-image' ),
			'base' => $this->tag,
			'params' => array(
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Source Type', 'darwin-image' ),
					'param_name' => 'type',
					'value' => array(
						esc_html__( 'ID', 'darwin-image' ) => 'id',
						esc_html__( 'Path', 'darwin-image' ) => 'path',
						),
					'admin_label' => true
					),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Image Size', 'darwin-image' ),
					'param_name' => 'size',
					'value' => $this->get_image_sizes(),
					'admin_label' => true
					),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Orientation', 'darwin-image' ),
					'param_name' => 'orientation',
					'value' => array(
						esc_html__( 'Horizontal', 'darwin-image' ) => 'horizontal',
						esc_html__( 'Vertical', 'darwin-image' ) => 'vertical',
						),
					'admin_label' => true
					),
				)
			)
		);
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
			'type' => 'id',
			'before' => 0,
			'after' => 0,
			'size' => 'medium',
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
			esc_attr_x( 'Before', 'Darwin before image alt text', 'darwin-image' ),
			esc_url( $atts['after'] ),
			esc_attr_x( 'After', 'Darwin after image alt text', 'darwin-image' )
		);
	}

}

new Darwin_Image;
