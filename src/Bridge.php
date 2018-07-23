<?php

namespace Bridge\Foundation;


if (!defined('ABSPATH'))
	exit;

final class Bridge {


	private static $instance;
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->start();
		}
		return self::$instance;
	}
	public function __clone() {
		_doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'bridge'), '1.6');
	}
	public function __wakeup() {
		_doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'bridge'), '1.6');
	}


	public function start() {
		$template = new \Bridge\Foundation\Template();
		$twig = \Bridge\Template\Twig_Extension::instance();


		$twig->add_filter('get_class', 'get_class');
		$twig->add_filter('get_type', 'get_type');
		$twig->add_filter('print_r', function( $arr ) {
			return print_r($arr, true);
		});
		$twig->add_filter('stripshortcodes', 'strip_shortcodes');
		$twig->add_filter('array', array(&$template, 'to_array'));
		$twig->add_filter('excerpt', 'wp_trim_words');
		$twig->add_filter('function', array(&$template, 'exec_function'));
		$twig->add_filter('pretags', array(&$template, 'twig_pretags'));
		$twig->add_filter('sanitize', 'sanitize_title');
		$twig->add_filter('shortcodes', 'do_shortcode');
		$twig->add_filter('time_ago', array(&$template, 'time_ago'));
		$twig->add_filter('wpautop', 'wpautop');
		$twig->add_filter('list', array(&$template, 'add_list_separators'));
		$twig->add_filter('date', array(&$template, 'intl_date'));
		$twig->add_filter('apply_filters', function() {
			$args = func_get_args();
			$tag = current(array_splice($args, 1, 1));
			return apply_filters_ref_array($tag, $args);
		} );

		$twig->add_escaper('esc_url', function( \Twig_Environment $env, $string ) {
			return esc_url($string);
		});
		$twig->add_escaper('wp_kses_post', function( \Twig_Environment $env, $string ) {
			return wp_kses_post($string);
		});
		$twig->add_escaper('esc_html', function( \Twig_Environment $env, $string ) {
			return esc_html($string);
		});
		$twig->add_escaper('esc_js', function( \Twig_Environment $env, $string ) {
			return esc_js($string);
		});


		$twig->add_function('widget', function( $widget_name = null ) {
			if ( is_active_sidebar($widget_name) ) :
				ob_start(); 
				?>
				<div id="secondary" class="widget-area" role="complementary">
					<?php dynamic_sidebar($widget_name); ?>
				</div>
				<?php 
				return ob_get_clean();
			endif;
		});


		$twig->add_function('action', function( $context ) {
			$args = func_get_args();
			array_shift($args);
			$args[] = $context;
			call_user_func_array('do_action', $args);
		}, array('needs_context' => true));

		$twig->add_function('function', array(&$template, 'exec_function'));
		$twig->add_function('fn', array(&$template, 'exec_function'));
		$twig->add_function('print_r', 'print_r');
		$twig->add_function('shortcode', 'do_shortcode');
		
		$twig->add_function('wp_menu', array(&$template, 'wp_menu'));
		$twig->add_function('bloginfo', function( $show = '', $filter = 'raw' ) {
			return get_bloginfo($show, $filter);
		} );
		$twig->add_function('__', function( $text, $domain = 'default' ) {
			return __($text, $domain);
		} );
		$twig->add_function('translate', function( $text, $domain = 'default' ) {
			return translate($text, $domain);
		} );
		$twig->add_function('_e', function( $text, $domain = 'default' ) {
			return _e($text, $domain);
		} );
		$twig->add_function('_n', function( $single, $plural, $number, $domain = 'default' ) {
			return _n($single, $plural, $number, $domain);
		} );
		$twig->add_function('_x', function( $text, $context, $domain = 'default' ) {
			return _x($text, $context, $domain);
		} );
		$twig->add_function('_ex', function( $text, $context, $domain = 'default' ) {
			return _ex($text, $context, $domain);
		} );
		$twig->add_function('_nx', function( $single, $plural, $number, $context, $domain = 'default' ) {
			return _nx($single, $plural, $number, $context, $domain);
		} );
		$twig->add_function('_n_noop', function( $singular, $plural, $domain = 'default' ) {
			return _n_noop($singular, $plural, $domain);
		} );
		$twig->add_function('_nx_noop', function( $singular, $plural, $context, $domain = 'default' ) {
			return _nx_noop($singular, $plural, $context, $domain);
		} );
		$twig->add_function('translate_nooped_plural', function( $nooped_plural, $count, $domain = 'default' ) {
			return translate_nooped_plural($nooped_plural, $count, $domain);
		});
	}
}


