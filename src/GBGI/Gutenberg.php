<?php 

namespace GBGI;

// -----------------------------------------------------------------

defined( 'ABSPATH' ) or die;

// -----------------------------------------------------------------

class Gutenberg {

    public static function register() {

        // Plugin
        wp_register_script(
            'gbgi-plugin',
            Common::plugin_url('/dist/plugin/plugin.build.js'),
            [
                'wp-plugins',
                'wp-components',
                'wp-edit-post',
                'wp-element',
                'wp-data',
                'wp-compose',
                'wp-api-request'
            ]
        );

        // Plugin Sidebar CSS
        wp_register_style(
            'gbgi-plugin-editor-sidebar',
            Common::plugin_url('dist/plugin/plugin.css')
        );

        // Gutenberg Block
        wp_register_script(
            'gbgi-gutenberg-block',
            Common::plugin_url('dist/blocks/gbgi-block/block.build.js'),
            [
                'wp-blocks', 
                'wp-element',
                'wp-components',
                'wp-editor',
            ]
        );

        // Block Style
        wp_register_style(
            'gbgi-gutenberg-block-style',
            Common::plugin_url('dist/blocks/gbgi-block/style.css')
        );

        // Register Gutenberg Block
        register_block_type( 
            'gbgi/gbgi-block', [
                'style' => 'gbgi-gutenberg-block-style',
                'editor_script' => 'gbgi-gutenberg-block',
                'render_callback' => '\GBGI\Gutenberg::render_block'
            ]
        );

    }

    public static function enqueue() {
        wp_enqueue_script('gbgi-plugin');
        wp_enqueue_style('gbgi-plugin-editor-sidebar');
    }

    public static function render_block($attrs){
        
        $game_info_json = array_key_exists('gameInfoJson', $attrs) ? 
            $attrs['gameInfoJson'] :
            "";

        $custom_template = array_key_exists('customTemplate', $attrs) ? 
            $attrs['customTemplate'] :
            "";

        if ($game_info_json == ''){
            return  "<!-- gbgi/gbgi-block - No Game Data -->";
        } 

        $decoded_json = json_decode($game_info_json, true);

        $template = '@gbgi/block_default.twig';

        if ($custom_template !== ''){
            $template =  "@gbgi-custom-template/" . $custom_template;
        }

        $context = [
            'game_info' => $decoded_json,
            'gb_icon_path' => Common::plugin_url('assets/gb.png')
        ];

        $comment_wrapper = "<!-- gbgi/gbgi-block - " . $template . " -->";

        return $comment_wrapper . 
            \Timber::compile($template, $context) . 
            $comment_wrapper;

    }


}



?>