<?php
/*
Plugin Name: Visual Composer Galleria
Plugin URI: http://wp-galleria.net/plugin
Description: Simple and smooth slider(works awesome on mobile)
Version: 1.1.1
Author: Mainerici Angel
Author URI: http://wp-galleria.net
License: GPLv2 or later
*/


if (!defined('ABSPATH')) die('-1');

class VCExtendAddonClass
{
    protected $html;

    protected $uniqueId;

    function __construct()
    {
        add_action('init', array($this, 'integrateWithVC'));

        add_shortcode('wp-galleria', array($this, 'renderHtmlOutput'));

        add_action('wp_enqueue_scripts', array($this, 'loadCssAndJs'));
    }

    public function integrateWithVC()
    {
        if (!defined('WPB_VC_VERSION')) {
            add_action('admin_notices', array($this, 'showVcVersionNotice'));
            return;
        }

        vc_map(array(
            "name" => __("WP Galleria", 'vc_extend'),
            "description" => __("Smmooth gallery that just works", 'vc_extend'),
            "base" => "wp-galleria",
            "class" => "",
            "icon" => plugins_url('assets/icon.png', __FILE__), // or css class name which you can reffer in your css file later. Example: "vc_extend_my_class"
            "params" => array(
                array(
                    "type" => "attach_images",
                    "holder" => "div",
                    "heading" => __("Content", 'vc_extend'),
                    "param_name" => "images",
                    "description" => __("Gallery Images", 'vc_extend')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "heading" => __("Animation", 'vc_extend'),
                    "param_name" => "mode",
                    "value" => [
                        'Slide' => '\'slide\'',
                        'Fade' => '\'fade\''
                    ],
                    'save_always' => true,
                    "description" => __("The way the slide change", 'vc_extend')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "heading" => __("Show Arrows", 'vc_extend'),
                    "param_name" => "controls",
                    "value" => [
                        'Yes' => 'true',
                        'No' => 'false'
                    ],
                    'save_always' => true,
                    "description" => __("Thumbnail navigation", 'vc_extend')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "heading" => __("Loop Slides", 'vc_extend'),
                    "param_name" => "loop",
                    "value" => [
                        'Yes' => 'true',
                        'No' => 'false'
                    ],
                    'save_always' => true,
                    "description" => __("Loop from last to first slide", 'vc_extend')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "heading" => __("Gallery", 'vc_extend'),
                    "param_name" => "gallery",
                    "value" => [
                        'Yes' => 'true',
                        'No' => 'false'
                    ],
                    'save_always' => true,
                    "description" => __("Enable navigation thumbnails", 'vc_extend')
                )
            )
        ));
    }

    public function renderHtmlOutput($atts, $content = null)
    {
        $this->uniqueId = uniqid();
        $images = explode(',', $atts['images']);
        $this->html = '<ul id="' . $this->uniqueId . '">';
        foreach ($images as $image) {
            if ($atts['gallery'] == 'false') {
                $this->html .= '<li>';
            } else {
                $thumbnail = wp_get_attachment_image_src($image, 'thumbnail');
                $this->html .= '<li data-thumb="' . $thumbnail[0] . '">';
            }
            $this->html .= wp_get_attachment_image($image, 'large');
            $this->html .= '</li>';
        }
        $this->html .= '</ul>';
        unset($atts['images']);
        $this->generateInitScript($atts);

        return $this->html;
    }

    public function generateInitScript($atts)
    {
        $this->html .= '<script>
                            jQuery(function () {
                                jQuery("#';
        $this->html .= $this->uniqueId;
        $this->html .= '").lightSlider({';
        foreach ($atts as $key => $value) {
            $this->html .= $key . ':' . $value . ',';
        }
        $this->html .= 'currentPagerPosition: \'middle\',thumbItem:3';
        $this->html .= '});
                    });
                    </script>';
    }

    public function loadCssAndJs()
    {
        wp_register_style('vc_extend_style', plugins_url('assets/lightslider.css', __FILE__));
        wp_enqueue_style('vc_extend_style');
        wp_enqueue_script('basic', plugins_url('assets/lightslider.js', __FILE__), array('jquery'), false, true);
    }

    public function showVcVersionNotice()
    {
        $plugin_data = get_plugin_data(__FILE__);
        echo '
        <div class="updated">
          <p>' . sprintf(__('<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'vc_extend'), $plugin_data['Name']) . '</p>
        </div > ';
    }
}

new VCExtendAddonClass();
