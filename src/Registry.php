<?php
namespace Bridge\Foundation;

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class Registry {

	public function _register_menu() {
		$menus = \Bridge\Element::type('menu')->query('locations')->results();
		if($menus) {
			foreach ($menus as $key => $menu) {
				if(isset($menu['segment']) && $menu['segment'] === 'wordpress') {
					register_nav_menu($key, __( $menu['title'], 'bridge' ) );
				}
			}
		}
		
	}

	public function _register_widget() {
		$sidebars = \Bridge\Element::type('widget')->query('all')->results();
		if($sidebars) {
			add_action('widgets_init', function() use ($sidebars) {
				foreach ($sidebars as $sidebar => $args) {
					register_sidebar($args);
				}

			});
		}
	}

	public function _register_api() {
		$apis =  \Bridge\Metadata::type('route_api')->query('all')->results();

		if($apis) {
			add_action('init', function () use($apis) {
				$callback = isset($route->callback) ? $route->callback : 'response_api';
				foreach ($apis as $key => $route) {
					register_rest_route( 'bridge/v2', '/' . $route->path, array(
						array(
							'methods'             => $route->methods ? $route->methods : 'GET',
							'callback'            => array(&$route->controller, $callback),
							'args'                => []
						)
					) );
				}
			}, 99);
		}
	}

	public function _register_type() {
		$schemas = \Bridge\Metadata::type('schema')->query('all')->results();
		if($schemas) {
			foreach ($schemas as $key => $schema) {
				if($schema->virtual && $schema->virtual_type === 'post_type') {
					if(!in_array($schema->id, ['post', 'page'])) {
						add_action('init', function () use ($schema) {
							$taxonomies = [];

							foreach ($schema->properties as $key => $property) {
								if(isset($property['relation']) && $property['relation'] === 'taxonomy') {
									$taxonomies[] = $property['relation_target'];
								}
							}
							register_post_type($schema->id, array_merge_recursive(array_merge(self::post_args(), $schema->meta), [
								'taxonomies' => $taxonomies
							]));
						}, 10);
					}
				}

				if($schema->virtual && $schema->virtual_type === 'taxonomy') {
					if(!in_array($schema->id, ['category', 'post_tag'])) {
						add_action('init', function () use ($schema) {
							$posttypes = [];
							foreach ($schema->properties as $key => $property) {
								if(isset($property['relation']) && $property['relation'] == 'post_type') {
									$posttypes[] = $property['relation_target'];
								}
							}
							register_taxonomy($schema->id, $posttypes, array_merge(self::term_args(), $schema->meta));

						}, 10);
					}
				}
			}
		}
	}

	static public function post_args() {
		return [
			'label' => 'Posts',
			'labels' => [
				'archives'              => __( 'Item Archives', 'bridge' ),
				'attributes'            => __( 'Item Attributes', 'bridge' ),
				'parent_item_colon'     => __( 'Parent Item:', 'bridge' ),
				'all_items'             => __( 'All Items', 'bridge' ),
				'add_new_item'          => __( 'Add New Item', 'bridge' ),
				'add_new'               => __( 'Add New', 'bridge' ),
				'new_item'              => __( 'New Item', 'bridge' ),
				'edit_item'             => __( 'Edit Item', 'bridge' ),
				'update_item'           => __( 'Update Item', 'bridge' ),
				'view_item'             => __( 'View Item', 'bridge' ),
				'view_items'            => __( 'View Items', 'bridge' ),
				'search_items'          => __( 'Search Item', 'bridge' ),
				'not_found'             => __( 'Not found', 'bridge' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'bridge' ),
				'featured_image'        => __( 'Featured Image', 'bridge' ),
				'set_featured_image'    => __( 'Set featured image', 'bridge' ),
				'remove_featured_image' => __( 'Remove featured image', 'bridge' ),
				'use_featured_image'    => __( 'Use as featured image', 'bridge' ),
				'insert_into_item'      => __( 'Insert into item', 'bridge' ),
				'uploaded_to_this_item' => __( 'Uploaded to this item', 'bridge' ),
				'items_list'            => __( 'Items list', 'bridge' ),
				'items_list_navigation' => __( 'Items list navigation', 'bridge' ),
				'filter_items_list'     => __( 'Filter items list', 'bridge' ),
			],
			'supports'				=> ['title'],
			'taxonomies'            => [],
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
		];
	}
	
	static public function term_args() {
		return [
			'labels'                     => [
				'all_items'                  => __( 'All Items', 'bridge' ),
				'parent_item'                => __( 'Parent Item', 'bridge' ),
				'parent_item_colon'          => __( 'Parent Item:', 'bridge' ),
				'new_item_name'              => __( 'New Item Name', 'bridge' ),
				'add_new_item'               => __( 'Add New Item', 'bridge' ),
				'edit_item'                  => __( 'Edit Item', 'bridge' ),
				'update_item'                => __( 'Update Item', 'bridge' ),
				'view_item'                  => __( 'View Item', 'bridge' ),
				'separate_items_with_commas' => __( 'Separate items with commas', 'bridge' ),
				'add_or_remove_items'        => __( 'Add or remove items', 'bridge' ),
				'choose_from_most_used'      => __( 'Choose from the most used', 'bridge' ),
				'popular_items'              => __( 'Popular Items', 'bridge' ),
				'search_items'               => __( 'Search Items', 'bridge' ),
				'not_found'                  => __( 'Not Found', 'bridge' ),
				'no_terms'                   => __( 'No items', 'bridge' ),
				'items_list'                 => __( 'Items list', 'bridge' ),
				'items_list_navigation'      => __( 'Items list navigation', 'bridge' ),
			],
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => false
		];
	}

}
