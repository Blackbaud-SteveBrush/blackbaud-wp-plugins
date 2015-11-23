<?php
/*
Plugin Name: Blackbaud: Carousel
Description: Twitter Bootstrap Carousels for your WordPress site. <em>(Requires&nbsp;Blackbaud:&nbsp;Assistant&nbsp;&amp;&nbsp;Libraries)</em>
Author: Blackbaud Interactive Services
Version: 0.0.1
Text Domain: blackbaud-carousel
*/


# Exit if accessed directly.
if (!defined('ABSPATH')) exit;


# EXECUTE WHEN BLACKBAUD IS READY.
function blackbaud_carousel_init($blackbaud) {


    # REGISTER.
    function config($blackbaud) {
        return array(
            'alias'               => 'blackbaud_carousel',
            'shortcode'           => 'blackbaud_carousel',
            'post_type'           => 'bb_carousel_slide',
            'taxonomy_slug'       => 'bb_carousel',
            'plugin_file'         => __FILE__,
            'plugin_basename'     => plugin_basename(__FILE__),
            'url_root'            => plugins_url('assets/', __FILE__),
            'templates_directory' => plugin_dir_path(__FILE__) . 'templates/',
        );
    }


    /**
     * Slides custom post type.
     */
    function slide_cpt($plugin, $blackbaud) {
        return array(
            'slug'        => $plugin->get('post_type'),
            'description' => __('Create any number of Bootstrap carousel slides.', 'blackbaud-carousel'),
            'supports'    => array('title', 'editor', 'thumbnail', 'page-attributes'),
            'menu_icon'   => 'dashicons-images-alt2',
            'labels'      => array (
                'name'               => __ ('Carousel Slides', 'blackbaud-carousel'),
                'singular_name'      => __ ('Slide', 'blackbaud-carousel'),
                'menu_name'          => _x ('Slides', 'admin menu', 'blackbaud-carousel'),
                'name_admin_bar'     => _x ('Slide', 'add new on admin bar', 'blackbaud-carousel'),
                'add_new'            => _x ('Add New', 'slide', 'blackbaud-carousel'),
                'add_new_item'       => __ ('Add New Slide', 'blackbaud-carousel'),
                'new_item'           => __ ('New Slide', 'blackbaud-carousel'),
                'edit_item'          => __ ('Edit Slide', 'blackbaud-carousel'),
                'view_item'          => __ ('View Slide', 'blackbaud-carousel'),
                'all_items'          => __ ('All Slides', 'blackbaud-carousel'),
                'search_items'       => __ ('Search Slides', 'blackbaud-carousel'),
                'parent_item_colon'  => __ ('Parent Slides:', 'blackbaud-carousel'),
                'not_found'          => __ ('No slides found.', 'blackbaud-carousel'),
                'not_found_in_trash' => __ ('No slides found in Trash.', 'blackbaud-carousel')
            ),
            'title_placeholder' => 'Enter slide title',
            'register_taxonomies'  => array(
                $plugin->get('taxonomy_slug') => array(
                    'labels' => array(
                        'name' => __('Carousels', 'blackbaud-carousel'),
                        'singular_name' => __('Carousel', 'blackbaud-carousel'),
                        'menu_name' => __('Carousels', 'blackbaud-carousel'),
                        'all_items' => __('All Carousels', 'blackbaud-carousel'),
                        'edit_item' => __('Edit Carousel', 'blackbaud-carousel'),
                        'view_item' => __('View Carousel', 'blackbaud-carousel'),
                        'update_item' => __('Update Carousel', 'blackbaud-carousel'),
                        'add_new_item' => __('Add New Carousel', 'blackbaud-carousel'),
                        'new_item_name' => __('New Carousel Name', 'blackbaud-carousel'),
                        'parent_item' => __('Parent Carousel', 'blackbaud-carousel'),
                        'parent_item_colon' => __('Parent Carousel:', 'blackbaud-carousel'),
                        'search_items' => __('Search Carousels', 'blackbaud-carousel'),
                        'popular_items' => __('Popular Carousels', 'blackbaud-carousel'),
                        'not_found' => __('No carousels found.', 'blackbaud-carousel')
                    ),
                    'public' => true,
                    'show_ui' => true,
                    'show_tagcloud' => false,
                    'hierarchical' => true
                )
            )
        );
    }


    /**
     * Additional content.
     */
    function slide_additional_content_metabox($plugin, $blackbaud) {
        return array(
            'label' => __('Additional Content', 'blackbaud-carousel'),
            'slug' => 'additional_content',
            'post_type' => $plugin->get('post_type'),
            'fields' => array(
                array(
                    'slug' => 'subtitle_1',
                    'label' => __('Subtitle 1:', 'blackbaud-carousel'),
                    'type' => 'text',
                    'attributes' => array(
                        'class' => 'form-control',
                        'maxlength' => '1500'
                    )
                ),
                array(
                    'slug' => 'subtitle_2',
                    'label' => __('Subtitle 2:', 'blackbaud-carousel'),
                    'type' => 'text',
                    'attributes' => array(
                        'class' => 'form-control',
                        'maxlength' => '1500'
                    )
                )
            )
        );
    }


    /**
     * Buttons.
     */
    function slide_buttons_metabox($plugin, $blackbaud) {
        return array(
            'label' => __('Buttons', 'blackbaud-carousel'),
            'slug' => 'buttons',
            'post_type' => $plugin->get('post_type'),
            'fields' => array(
                array(
                    'slug' => 'primary_button_label',
                    'label' => __('Primary Button Label:', 'blackbaud-carousel'),
                    'type' => 'text',
                    'attributes' => array(
                        'class' => 'form-control',
                        'maxlength' => '1500'
                    )
                ),
                array(
                    'slug' => 'primary_button_link',
                    'label' => __('Primary Button Link:', 'blackbaud-carousel'),
                    'type' => 'text',
                    'attributes' => array(
                        'class' => 'form-control',
                        'maxlength' => '1500',
                        'placeholder' => 'http://'
                    )
                ),
                array(
                    'slug' => 'secondary_button_label',
                    'label' => __('Secondary Button Label:', 'blackbaud-carousel'),
                    'type' => 'text',
                    'attributes' => array(
                        'class' => 'form-control',
                        'maxlength' => '1500'
                    )
                ),
                array(
                    'slug' => 'secondary_button_link',
                    'label' => __('Secondary Button Link:', 'blackbaud-carousel'),
                    'type' => 'text',
                    'attributes' => array(
                        'class' => 'form-control',
                        'maxlength' => '1500',
                        'placeholder' => 'http://'
                    )
                )
            )
        );
    }


    /**
     * Slides: Advanced content.
     */
    function slide_advanced_content_metabox($plugin, $blackbaud) {
        return array(
            'label' => __('Advanced', 'blackbaud-carousel'),
            'slug' => 'advanced_content',
            'post_type' => $plugin->get('post_type'),
            'fields' => array(
                array(
                    'slug' => 'css_class',
                    'label' => __('CSS class:', 'blackbaud-carousel'),
                    'type' => 'text',
                    'attributes' => array(
                        'class' => 'form-control',
                        'maxlength' => '500',
                        'spellcheck' => 'false'
                    )
                ),
                array(
                    'slug' => 'html_after',
                    'label' => __('HTML after:', 'blackbaud-carousel'),
                    'type' => 'textarea',
                    'attributes'  => array(
                        'class' => 'form-control accepts-code',
                        'maxlength' => '5000',
                        'spellcheck' => 'false'
                    )
                )
            )
        );
    }


    /**
     * Sortable Columns.
     */
    function slide_sortable_columns($plugin, $blackbaud) {
        return array(
            'post_type' => $plugin->get('post_type'),
            'columns' => array(
                'blackbaud_carousel_category' => array(
                    'label' => __('Carousel', 'blackbaud-carousel'),
                    'value' => function ($data) use ($plugin)
                    {
                        $terms = get_the_terms($data['post_id'], $plugin->get('taxonomy_slug'));
                        return $terms[0]->name;
                    }
                ),
                'blackbaud_carousel_order' => array(
                    'label' => __('Order', 'blackbaud-carousel'),
                    'value' => function ($data) use ($plugin)
                    {
                        $post = get_post($data['post_id']);
                        return $post->menu_order . '';
                    }
                ),
                'blackbaud_carousel_shortcode' => array(
                    'label' => __('Shortcode', 'blackbaud-carousel'),
                    'value' => function ($data) use ($plugin, $blackbaud)
                    {
                        $terms = get_the_terms($data['post_id'], $plugin->get('taxonomy_slug'));
                        $carousel_slug = $terms[0]->slug;
                        return '<code class="blackbaud-selectable blackbaud-shortcode-snippet" title="Click to select the shortcode. Ctrl+C(or Cmd+C) to copy to your clipboard.">[' . $plugin->get('shortcode') . ' slug="' . $carousel_slug . '"]</code>';
                    }
                )
            )
        );
    }


    /**
     * Carousel: Attributes.
     */
    function carousel_attributes($plugin, $blackbaud) {
        return array(
            'taxonomy' => $plugin->get('taxonomy_slug'),
            'fields' => array(
                array(
                    'slug' => 'transition_type',
                    'label' => __('Transition type:', 'blackbaud-carousel'),
                    'type' => 'select',
                    'default' => 'slide',
                    'options' => array(
                        'Slide' => 'slide',
                        'Fade' => 'fade'
                    )
                ),
                array(
                    'slug' => 'transition_speed',
                    'label' => __('Transition speed:', 'blackbaud-carousel'),
                    'type' => 'text',
                    'helplet' => 'In milliseconds.',
                    'default' => '1000',
                    'attributes' => array(
                        'maxlength' => '500'
                    )
                ),
                array(
                    'slug' => 'interval',
                    'label' => __('Duration between slides:', 'blackbaud-carousel'),
                    'type' => 'text',
                    'helplet' => 'In milliseconds.',
                    'default' => '3000',
                    'attributes' => array(
                        'maxlength' => '500'
                    )
                ),
                array(
                    'slug' => 'navigation_previous',
                    'label' => __('Navigation button label (previous):', 'blackbaud-carousel'),
                    'type' => 'text',
                    'helplet' => 'Accepts HTML',
                    'default' => '<i class="glyphicon glyphicon-chevron-left"></i>',
                    'attributes' => array(
                        'maxlength' => '500',
                        'placeholder' => 'e.g., &amp;larr;',
                    )
                ),
                array(
                    'slug' => 'navigation_next',
                    'label' => __('Navigation button label (next):', 'blackbaud-carousel'),
                    'type' => 'text',
                    'helplet' => 'Accepts HTML',
                    'default' => '<i class="glyphicon glyphicon-chevron-right"></i>',
                    'attributes' => array(
                        'maxlength' => '500',
                        'placeholder' => 'e.g., &amp;rarr;',
                    )
                ),
                array(
                    'slug' => 'num_slides_per_iteration',
                    'label' => __('Num. slides per iteration:', 'blackbaud-carousel'),
                    'type' => 'text',
                    'helplet' => "This value lets you display more than one slide for every iteration. For example, display a group of three slides each time the carousel progresses.",
                    'default' => '1',
                    'attributes' => array(
                        'maxlength' => '5'
                    )
                ),
                array(
                    'slug' => 'css_class',
                    'label' => __('CSS class:', 'blackbaud-carousel'),
                    'type' => 'text',
                    'attributes' => array(
                        'maxlength' => '500'
                    ),
                    'parent_attributes' => array(
                        'class' => 'my-class'
                    )
                ),
                array(
                    'slug' => 'auto_play',
                    'label' => __('Auto play', 'blackbaud-carousel'),
                    'type' => 'checkbox',
                    'default' => 'on'
                ),
                array(
                    'slug' => 'loop',
                    'label' => __('Loop the presentation', 'blackbaud-carousel'),
                    'type' => 'checkbox',
                    'default' => 'on'
                ),
                array(
                    'slug' => 'pause',
                    'label' => __('Pause on-hover', 'blackbaud-carousel'),
                    'type' => 'checkbox',
                    'default' => 'off'
                ),
                array(
                    'slug' => 'image_backgrounds',
                    'label' => __('Slide images are backgrounds', 'blackbaud-carousel'),
                    'type' => 'checkbox',
                    'default' => 'off'
                ),
                array(
                    'slug' => 'random_start',
                    'label' => __('First slide is random', 'blackbaud-carousel'),
                    'type' => 'checkbox',
                    'default' => 'off'
                )
            )
        );
    }


    /**
     * Carousel shortcode.
     */
    function carousel_shortcode($plugin) {
        return array(
            'slug' => $plugin->get('shortcode'),
            'output' => function ($data) use ($plugin) {
                $term_slug = $data['slug'];
                $carousel = get_term_by('slug', $term_slug, $plugin->get('taxonomy_slug'));
                $data = get_option('taxonomy_' . $carousel->term_id);

                $data['id'] = uniqid();
                $data['slides'] = get_posts(array(
                        'post_type' => $plugin->get('post_type'),
                        'orderby' => 'menu_order',
                        'order' => 'ASC',
                        'showposts' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => $plugin->get('taxonomy_slug'),
                                'field' => 'slug',
                                'terms' => $term_slug
                            )
                        )
                    ));
                $data['starting_index'] = 0;
                $data['num_slides'] = count($data['slides']);
                $data['num_slides_per_iteration'] = (isset($data['num_slides_per_iteration']) && $data['num_slides_per_iteration'] != 0) ? (int) $data['num_slides_per_iteration'] : 1;
                $data['column_width'] = round(12 / $data['num_slides_per_iteration']);


                # Set a random starting slide.
                if (isset($data['random_start']) && $data['random_start'] == 'on') {
                    $data['starting_index'] = array_rand($data['slides']);
                }

                # Add custom fields to each slide.
                foreach ($data['slides'] as $k => $post) {
                    $post->fields = get_post_custom($post->ID);
                }

                # Display the template HTML.
                return $plugin->get_template('carousel.blackbaud-carousel.php', $data);
            }
        );
    }



    $plugin = $blackbaud->register('config');
    $plugin->forge('custom_post_type', 'slide_cpt');
    $plugin->forge('meta_box', 'slide_additional_content_metabox');
    $plugin->forge('meta_box', 'slide_buttons_metabox');
    $plugin->forge('meta_box', 'slide_advanced_content_metabox');
    $plugin->forge('post_sortable_columns', 'slide_sortable_columns');
    $plugin->forge('taxonomy_fields', 'carousel_attributes');
    $plugin->forge('shortcode', 'carousel_shortcode');
    $plugin->forge('updater');
}



# SUBSCRIBE TO BLACKBAUD'S READY EVENT.
add_action('blackbaud_ready', 'blackbaud_carousel_init');
