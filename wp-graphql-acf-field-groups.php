<?php

/**
 * Plugin Name: WP GraphQL ACF Field Groups
 * Plugin URI: https://github.com/webdeveducation/wp-graphql-acf-field-groups
 * Description: Query ACF field groups in WP GraphQL.
 * Author: WebDevEducation 
 * Author URI: https://webdeveducation.com
 * Version: 0.1.0
 * Requires at least: 6.0
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */


if (!defined('ABSPATH')) {
	die('Silence is golden.');
}

if (!class_exists('WPGraphQLACFFieldGroups')) {
	final class WPGraphQLACFFieldGroups {
		private static $instance;
		public static function instance() {
			if (!isset(self::$instance)) {
				self::$instance = new WPGraphQLACFFieldGroups();
			}

			return self::$instance;
		}

		public function init() {
      add_action( 'graphql_register_types', function () {
        add_action( 'graphql_register_types', function ($type_registry) {
          register_graphql_scalar('JSON', [
            'serialize' => function ($value) {
              return json_decode($value);
            }
          ]);
        });	

        register_graphql_field( 'RootQuery', 'acfFieldGroup', [
          'type' => 'JSON',
          'description' => 'Return the field group data',
          'args' => ['fieldGroupId' => [
            'description' => "The field group ID",
            'type' => "String"
          ]],
          'resolve' => function($root, $args) {
            //wp_send_json(['args' => $args]);
            if(function_exists('acf_get_field_group')){
              $field_group = acf_get_field_group($args['fieldGroupId']);
              $field_group['fields'] = acf_get_fields($args['fieldGroupId']);
              return wp_json_encode($field_group);
            }
          }
        ]);
      });
		}
	}
}

WPGraphQLACFFieldGroups::instance()->init();

?>