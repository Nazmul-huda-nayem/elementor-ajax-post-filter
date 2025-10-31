<?php
/**
 * Plugin Name: Elementor Post Filter
 * Description: Custom Elementor widget for AJAX post filtering by categories and tags
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: elementor-post-filter
 */

if (!defined('ABSPATH')) {
    exit;
}

define('EPF_VERSION', '1.0.0');
define('EPF_PATH', plugin_dir_path(__FILE__));
define('EPF_URL', plugin_dir_url(__FILE__));

class Elementor_Post_Filter {
    
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        add_action('plugins_loaded', [$this, 'init']);
    }
    
    public function init() {
        // Check if Elementor is installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
            return;
        }
        
        // Register widget
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        
        // Register widget category
        add_action('elementor/elements/categories_registered', [$this, 'add_widget_category']);
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        
        // Register AJAX actions
        add_action('wp_ajax_epf_filter_posts', [$this, 'ajax_filter_posts']);
        add_action('wp_ajax_nopriv_epf_filter_posts', [$this, 'ajax_filter_posts']);
    }
    
    public function admin_notice_missing_elementor() {
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'elementor-post-filter'),
            '<strong>' . esc_html__('Elementor Post Filter', 'elementor-post-filter') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'elementor-post-filter') . '</strong>'
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    public function register_widgets($widgets_manager) {
        require_once EPF_PATH . 'widgets/post-filter-widget.php';
        $widgets_manager->register(new \Elementor_Post_Filter_Widget());
    }
    
    public function add_widget_category($elements_manager) {
        $elements_manager->add_category(
            'post-filter',
            [
                'title' => esc_html__('Post Filter', 'elementor-post-filter'),
                'icon' => 'fa fa-plug',
            ]
        );
    }
    
    public function enqueue_scripts() {
        // Enqueue styles
        wp_enqueue_style(
            'epf-style',
            EPF_URL . 'assets/css/style.css',
            [],
            EPF_VERSION
        );
        
        // Enqueue script
        wp_enqueue_script(
            'epf-script',
            EPF_URL . 'assets/js/script.js',
            ['jquery'],
            EPF_VERSION,
            true // Load in footer
        );
        
        // Localize script - make sure this runs
        wp_localize_script('epf-script', 'epfAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('epf_nonce'),
            'debug' => defined('WP_DEBUG') && WP_DEBUG
        ]);
    }
    
   public function ajax_filter_posts() {
    check_ajax_referer('epf_nonce', 'nonce');
    
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $tag = isset($_POST['tag']) ? sanitize_text_field($_POST['tag']) : '';
    $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'post';
    $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 6;
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $layout = isset($_POST['layout']) ? sanitize_text_field($_POST['layout']) : 'grid';
    $columns = isset($_POST['columns']) ? intval($_POST['columns']) : 3;
    $pagination_type = isset($_POST['pagination_type']) ? sanitize_text_field($_POST['pagination_type']) : 'numbers';
    $is_load_more = isset($_POST['is_load_more']) && $_POST['is_load_more'] === 'true';
    
    // Visibility settings
    $settings = [
        'layout' => $layout,
        'show_thumbnail' => isset($_POST['show_thumbnail']) ? sanitize_text_field($_POST['show_thumbnail']) : 'yes',
        'show_category_badge' => isset($_POST['show_category_badge']) ? sanitize_text_field($_POST['show_category_badge']) : 'yes',
        'show_title' => isset($_POST['show_title']) ? sanitize_text_field($_POST['show_title']) : 'yes',
        'show_excerpt' => isset($_POST['show_excerpt']) ? sanitize_text_field($_POST['show_excerpt']) : 'yes',
        'show_meta' => isset($_POST['show_meta']) ? sanitize_text_field($_POST['show_meta']) : 'yes',
        'excerpt_length' => isset($_POST['excerpt_length']) ? intval($_POST['excerpt_length']) : 20,
    ];
    
    $args = [
        'post_type' => $post_type,
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
        'post_status' => 'publish'
    ];
    
    $tax_query = [];
    
    if (!empty($category)) {
        $tax_query[] = [
            'taxonomy' => 'category',
            'field' => 'slug',
            'terms' => $category
        ];
    }
    
    if (!empty($tag)) {
        $tax_query[] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => $tag
        ];
    }
    
    if (count($tax_query) > 0) {
        $args['tax_query'] = $tax_query;
        if (count($tax_query) > 1) {
            $args['tax_query']['relation'] = 'AND';
        }
    }
    
    $query = new WP_Query($args);
    
    $posts_html = '';
    $pagination_html = '';
    
    if ($query->have_posts()) {
        // For load more, only return posts HTML (no wrapper, no pagination)
        if ($is_load_more) {
            ob_start();
            while ($query->have_posts()) {
                $query->the_post();
                $this->render_post_item_ajax($settings);
            }
            $posts_html = ob_get_clean();
        } else {
            // For regular filter, return wrapper + posts
            ob_start();
            echo '<div class="epf-posts-wrapper epf-layout-' . esc_attr($layout) . ' epf-columns-' . esc_attr($columns) . '">';
            
            while ($query->have_posts()) {
                $query->the_post();
                $this->render_post_item_ajax($settings);
            }
            
            echo '</div>'; // Close .epf-posts-wrapper
            $posts_html = ob_get_clean();
            
            // Render pagination separately
            if ($query->max_num_pages > 1 && $pagination_type !== 'none') {
                ob_start();
                if ($pagination_type === 'load_more') {
                    $load_more_text = isset($_POST['load_more_text']) ? sanitize_text_field($_POST['load_more_text']) : 'Load More';
                    echo '<div class="epf-load-more-wrapper" data-max-pages="' . esc_attr($query->max_num_pages) . '" data-current-page="' . esc_attr($paged) . '">';
                    echo '<button class="epf-load-more-btn" type="button"><span class="epf-btn-text">' . esc_html($load_more_text) . '</span></button>';
                    echo '</div>';
                } else {
                    echo '<div class="epf-pagination">';
                    echo paginate_links([
                        'total' => $query->max_num_pages,
                        'current' => $paged,
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                    ]);
                    echo '</div>';
                }
                $pagination_html = ob_get_clean();
            }
        }
    } else {
        if (!$is_load_more) {
            $posts_html = '<p class="epf-no-posts">' . esc_html__('No posts found.', 'elementor-post-filter') . '</p>';
        }
    }
    
    wp_reset_postdata();
    
    $response = [
        'success' => true,
        'html' => $posts_html,
        'pagination' => $pagination_html,
        'found_posts' => $query->found_posts,
        'max_pages' => $query->max_num_pages,
        'current_page' => $paged,
        'is_load_more' => $is_load_more
    ];
    
    wp_send_json($response);
}
    
    private function render_post_item_ajax($settings) {
        $show_thumbnail = isset($settings['show_thumbnail']) ? $settings['show_thumbnail'] : 'yes';
        $show_category_badge = isset($settings['show_category_badge']) ? $settings['show_category_badge'] : 'yes';
        $show_title = isset($settings['show_title']) ? $settings['show_title'] : 'yes';
        $show_excerpt = isset($settings['show_excerpt']) ? $settings['show_excerpt'] : 'yes';
        $show_meta = isset($settings['show_meta']) ? $settings['show_meta'] : 'yes';
        $excerpt_length = isset($settings['excerpt_length']) ? $settings['excerpt_length'] : 20;
        
        ?>
        <article class="epf-post-item">
            <?php if ($show_thumbnail === 'yes' && has_post_thumbnail()): ?>
                <div class="epf-post-thumbnail">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('medium'); ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="epf-post-content">
                <?php if ($show_category_badge === 'yes'):
                    $categories = get_the_category();
                    if (!empty($categories)):
                ?>
                    <div class="epf-post-category">
                        <?php echo esc_html($categories[0]->name); ?>
                    </div>
                <?php 
                    endif;
                endif; 
                ?>
                
                <?php if ($show_title === 'yes'): ?>
                <h3 class="epf-post-title">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_title(); ?>
                    </a>
                </h3>
                <?php endif; ?>
                
                <?php if ($show_excerpt === 'yes' && $settings['layout'] !== 'minimal'): ?>
                    <div class="epf-post-excerpt">
                        <?php echo wp_trim_words(get_the_excerpt(), $excerpt_length); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($show_meta === 'yes' && $settings['layout'] !== 'minimal'): ?>
                    <div class="epf-post-meta">
                        <span class="epf-post-date"><?php echo get_the_date(); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }
}

Elementor_Post_Filter::instance();