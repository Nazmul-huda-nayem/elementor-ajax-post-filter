<?php
if (!defined('ABSPATH')) {
    exit;
}

class Elementor_Post_Filter_Widget extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'post_filter';
    }
    
    public function get_title() {
        return esc_html__('Post Filter', 'elementor-post-filter');
    }
    
    public function get_icon() {
        return 'eicon-posts-grid';
    }
    
    public function get_categories() {
        return ['post-filter'];
    }
    
    public function get_keywords() {
        return ['post', 'filter', 'category', 'tag', 'ajax'];
    }
    
    public function get_script_depends() {
        return ['jquery', 'epf-script'];
    }
    
    public function get_style_depends() {
        return ['epf-style'];
    }
    
    protected function register_controls() {
        
        // Content Tab - Query Section
        $this->start_controls_section(
            'query_section',
            [
                'label' => esc_html__('Query', 'elementor-post-filter'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'post_type',
            [
                'label' => esc_html__('Post Type', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'post',
                'options' => $this->get_post_types(),
            ]
        );
        
        $this->add_control(
            'posts_per_page',
            [
                'label' => esc_html__('Posts Per Page', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 6,
                'min' => 1,
                'max' => 100,
            ]
        );
        
        $this->add_control(
            'show_category_filter',
            [
                'label' => esc_html__('Show Category Filter', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elementor-post-filter'),
                'label_off' => esc_html__('No', 'elementor-post-filter'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_tag_filter',
            [
                'label' => esc_html__('Show Tag Filter', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elementor-post-filter'),
                'label_off' => esc_html__('No', 'elementor-post-filter'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_post_count',
            [
                'label' => esc_html__('Show Post Count', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elementor-post-filter'),
                'label_off' => esc_html__('No', 'elementor-post-filter'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_recent_posts',
            [
                'label' => esc_html__('Show Recent Posts', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elementor-post-filter'),
                'label_off' => esc_html__('No', 'elementor-post-filter'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'recent_posts_count',
            [
                'label' => esc_html__('Recent Posts Count', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 5,
                'min' => 1,
                'max' => 10,
                'condition' => [
                    'show_recent_posts' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'pagination_type',
            [
                'label' => esc_html__('Pagination Type', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'numbers',
                'options' => [
                    'numbers' => esc_html__('Numbers', 'elementor-post-filter'),
                    'load_more' => esc_html__('Load More Button', 'elementor-post-filter'),
                    'none' => esc_html__('None', 'elementor-post-filter'),
                ],
            ]
        );
        
        $this->add_control(
            'load_more_text',
            [
                'label' => esc_html__('Load More Text', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Load More', 'elementor-post-filter'),
                'condition' => [
                    'pagination_type' => 'load_more',
                ],
            ]
        );
        
        $this->add_control(
            'loading_text',
            [
                'label' => esc_html__('Loading Text', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Loading...', 'elementor-post-filter'),
                'condition' => [
                    'pagination_type' => 'load_more',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Layout Section
        $this->start_controls_section(
            'layout_section',
            [
                'label' => esc_html__('Layout', 'elementor-post-filter'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'layout',
            [
                'label' => esc_html__('Layout', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid', 'elementor-post-filter'),
                    'list' => esc_html__('List', 'elementor-post-filter'),
                    'minimal' => esc_html__('Minimal', 'elementor-post-filter'),
                ],
            ]
        );
        
        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'condition' => [
                    'layout' => 'grid',
                ],
                'selectors' => [
                    '{{WRAPPER}} .epf-posts-wrapper.epf-layout-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'column_gap',
            [
                'label' => esc_html__('Column Gap', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 24,
                ],
                'selectors' => [
                    '{{WRAPPER}} .epf-posts-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Visibility Section
        $this->start_controls_section(
            'visibility_section',
            [
                'label' => esc_html__('Element Visibility', 'elementor-post-filter'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'show_thumbnail',
            [
                'label' => esc_html__('Show Thumbnail', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elementor-post-filter'),
                'label_off' => esc_html__('No', 'elementor-post-filter'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_category_badge',
            [
                'label' => esc_html__('Show Category Badge', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elementor-post-filter'),
                'label_off' => esc_html__('No', 'elementor-post-filter'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_title',
            [
                'label' => esc_html__('Show Title', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elementor-post-filter'),
                'label_off' => esc_html__('No', 'elementor-post-filter'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_excerpt',
            [
                'label' => esc_html__('Show Excerpt', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elementor-post-filter'),
                'label_off' => esc_html__('No', 'elementor-post-filter'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'excerpt_length',
            [
                'label' => esc_html__('Excerpt Length', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 20,
                'min' => 5,
                'max' => 100,
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'show_meta',
            [
                'label' => esc_html__('Show Meta (Date)', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elementor-post-filter'),
                'label_off' => esc_html__('No', 'elementor-post-filter'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Tab - Filter Section
        $this->start_controls_section(
            'filter_style_section',
            [
                'label' => esc_html__('Filter', 'elementor-post-filter'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'filter_heading_color',
            [
                'label' => esc_html__('Heading Color', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .epf-filter-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'filter_heading_typography',
                'selector' => '{{WRAPPER}} .epf-filter-title',
            ]
        );
        
        $this->add_control(
            'filter_item_color',
            [
                'label' => esc_html__('Item Color', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .epf-filter-item' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'filter_item_active_color',
            [
                'label' => esc_html__('Active Color', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .epf-filter-item.active' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'filter_item_active_bg',
            [
                'label' => esc_html__('Active Background', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .epf-filter-item.active' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'filter_item_padding',
            [
                'label' => esc_html__('Item Padding', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .epf-filter-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'filter_item_border_radius',
            [
                'label' => esc_html__('Item Border Radius', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .epf-filter-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'filter_item_gap',
            [
                'label' => esc_html__('Items Gap', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .epf-filter-items' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'filter_item_typography',
                'selector' => '{{WRAPPER}} .epf-filter-item',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Tab - Post Card Section
        $this->start_controls_section(
            'card_style_section',
            [
                'label' => esc_html__('Post Card', 'elementor-post-filter'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'card_background',
            [
                'label' => esc_html__('Background Color', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .epf-post-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .epf-post-item',
            ]
        );
        
        $this->add_control(
            'card_border_radius',
            [
                'label' => esc_html__('Border Radius', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .epf-post-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .epf-post-thumbnail img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .epf-post-item',
            ]
        );
        
        $this->add_responsive_control(
            'card_padding',
            [
                'label' => esc_html__('Padding', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .epf-post-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_hover_shadow',
                'label' => esc_html__('Hover Shadow', 'elementor-post-filter'),
                'selector' => '{{WRAPPER}} .epf-post-item:hover',
            ]
        );
        
        $this->add_control(
            'card_transition',
            [
                'label' => esc_html__('Transition Duration (ms)', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 50,
                    ],
                ],
                'default' => [
                    'size' => 300,
                ],
                'selectors' => [
                    '{{WRAPPER}} .epf-post-item' => 'transition: all {{SIZE}}ms ease;',
                ],
            ]
        );
        
        $this->add_control(
            'thumbnail_aspect_ratio',
            [
                'label' => esc_html__('Thumbnail Aspect Ratio', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '66.67',
                'options' => [
                    '56.25' => '16:9',
                    '66.67' => '3:2',
                    '75' => '4:3',
                    '100' => '1:1',
                    '133.33' => '3:4',
                ],
                'selectors' => [
                    '{{WRAPPER}} .epf-post-thumbnail' => 'padding-top: {{VALUE}}%;',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Style Tab - Title Section
        $this->start_controls_section(
            'title_style_section',
            [
                'label' => esc_html__('Title', 'elementor-post-filter'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .epf-post-title a' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'title_hover_color',
            [
                'label' => esc_html__('Hover Color', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .epf-post-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .epf-post-title',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Tab - Category Section
        $this->start_controls_section(
            'category_style_section',
            [
                'label' => esc_html__('Category Badge', 'elementor-post-filter'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'category_color',
            [
                'label' => esc_html__('Color', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .epf-post-category' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'category_background',
            [
                'label' => esc_html__('Background', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .epf-post-category' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'category_typography',
                'selector' => '{{WRAPPER}} .epf-post-category',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Tab - Excerpt Section
        $this->start_controls_section(
            'excerpt_style_section',
            [
                'label' => esc_html__('Excerpt', 'elementor-post-filter'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'excerpt_color',
            [
                'label' => esc_html__('Color', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .epf-post-excerpt' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'excerpt_typography',
                'selector' => '{{WRAPPER}} .epf-post-excerpt',
            ]
        );
        
        $this->add_responsive_control(
            'excerpt_spacing',
            [
                'label' => esc_html__('Bottom Spacing', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .epf-post-excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Style Tab - Recent Posts Section
        $this->start_controls_section(
            'recent_posts_style_section',
            [
                'label' => esc_html__('Recent Posts', 'elementor-post-filter'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_recent_posts' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'recent_posts_bg',
            [
                'label' => esc_html__('Background Color', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .epf-recent-posts' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'recent_posts_border',
                'selector' => '{{WRAPPER}} .epf-recent-posts',
            ]
        );
        
        $this->add_control(
            'recent_posts_border_radius',
            [
                'label' => esc_html__('Border Radius', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .epf-recent-posts' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'recent_posts_padding',
            [
                'label' => esc_html__('Padding', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .epf-recent-posts' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'recent_item_heading',
            [
                'label' => esc_html__('Recent Item', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        
        $this->add_control(
            'recent_item_title_color',
            [
                'label' => esc_html__('Title Color', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .epf-recent-item-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'recent_item_title_hover_color',
            [
                'label' => esc_html__('Title Hover Color', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .epf-recent-item-title:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'recent_item_typography',
                'selector' => '{{WRAPPER}} .epf-recent-item-title',
            ]
        );
        
        $this->add_responsive_control(
            'recent_item_gap',
            [
                'label' => esc_html__('Items Gap', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .epf-recent-posts-list' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'recent_thumbnail_size',
            [
                'label' => esc_html__('Thumbnail Size', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 40,
                        'max' => 150,
                    ],
                ],
                'default' => [
                    'size' => 60,
                ],
                'selectors' => [
                    '{{WRAPPER}} .epf-recent-item-thumbnail' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Style Tab - Load More Button Section
        $this->start_controls_section(
            'load_more_style_section',
            [
                'label' => esc_html__('Load More Button', 'elementor-post-filter'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'pagination_type' => 'load_more',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'load_more_typography',
                'selector' => '{{WRAPPER}} .epf-load-more-btn',
            ]
        );
        
        $this->start_controls_tabs('load_more_tabs');
        
        // Normal State
        $this->start_controls_tab(
            'load_more_normal',
            [
                'label' => esc_html__('Normal', 'elementor-post-filter'),
            ]
        );
        
        $this->add_control(
            'load_more_color',
            [
                'label' => esc_html__('Text Color', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .epf-load-more-btn' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'load_more_bg',
            [
                'label' => esc_html__('Background Color', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ec4899',
                'selectors' => [
                    '{{WRAPPER}} .epf-load-more-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'load_more_shadow',
                'selector' => '{{WRAPPER}} .epf-load-more-btn',
            ]
        );
        
        $this->end_controls_tab();
        
        // Hover State
        $this->start_controls_tab(
            'load_more_hover',
            [
                'label' => esc_html__('Hover', 'elementor-post-filter'),
            ]
        );
        
        $this->add_control(
            'load_more_hover_color',
            [
                'label' => esc_html__('Text Color', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .epf-load-more-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'load_more_hover_bg',
            [
                'label' => esc_html__('Background Color', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#db2777',
                'selectors' => [
                    '{{WRAPPER}} .epf-load-more-btn:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'load_more_hover_shadow',
                'selector' => '{{WRAPPER}} .epf-load-more-btn:hover',
            ]
        );
        
        $this->add_control(
            'load_more_hover_transform',
            [
                'label' => esc_html__('Hover Transform', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'translateY(-2px)',
                'options' => [
                    'none' => esc_html__('None', 'elementor-post-filter'),
                    'translateY(-2px)' => esc_html__('Move Up', 'elementor-post-filter'),
                    'scale(1.05)' => esc_html__('Scale Up', 'elementor-post-filter'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .epf-load-more-btn:hover' => 'transform: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->add_responsive_control(
            'load_more_padding',
            [
                'label' => esc_html__('Padding', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'default' => [
                    'top' => 12,
                    'right' => 40,
                    'bottom' => 12,
                    'left' => 40,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .epf-load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );
        
        $this->add_responsive_control(
            'load_more_margin',
            [
                'label' => esc_html__('Margin', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .epf-load-more-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'load_more_border',
                'selector' => '{{WRAPPER}} .epf-load-more-btn',
            ]
        );
        
        $this->add_control(
            'load_more_border_radius',
            [
                'label' => esc_html__('Border Radius', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => 6,
                    'right' => 6,
                    'bottom' => 6,
                    'left' => 6,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .epf-load-more-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'load_more_align',
            [
                'label' => esc_html__('Alignment', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'elementor-post-filter'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'elementor-post-filter'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'elementor-post-filter'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .epf-load-more-wrapper' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'load_more_icon',
            [
                'label' => esc_html__('Icon', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'separator' => 'before',
            ]
        );
        
        $this->add_control(
            'load_more_icon_position',
            [
                'label' => esc_html__('Icon Position', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'after',
                'options' => [
                    'before' => esc_html__('Before', 'elementor-post-filter'),
                    'after' => esc_html__('After', 'elementor-post-filter'),
                ],
            ]
        );
        
        $this->add_responsive_control(
            'load_more_icon_spacing',
            [
                'label' => esc_html__('Icon Spacing', 'elementor-post-filter'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .epf-load-more-btn .epf-btn-icon-before' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .epf-load-more-btn .epf-btn-icon-after' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        
        // Prevent duplicate rendering
        static $rendered_widgets = [];
        $widget_key = $widget_id . '_' . get_the_ID();
        
        if (isset($rendered_widgets[$widget_key])) {
            return; // Already rendered, skip
        }
        $rendered_widgets[$widget_key] = true;
        
        // Add inline script to ensure initialization
        ?>
        <script>
        jQuery(document).ready(function($) {
            console.log('EPF: Widget <?php echo esc_js($widget_id); ?> loaded');
        });
        </script>
        
        <div class="epf-post-filter-wrapper" data-widget-id="<?php echo esc_attr($widget_id); ?>">
            
            <div class="epf-filters">
                <?php if ($settings['show_category_filter'] === 'yes'): ?>
                    <div class="epf-filter-group epf-category-filter">
                        <h3 class="epf-filter-title"><?php echo esc_html__('Categories', 'elementor-post-filter'); ?></h3>
                        <?php $this->render_category_filter($settings); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($settings['show_tag_filter'] === 'yes'): ?>
                    <div class="epf-filter-group epf-tag-filter">
                        <h3 class="epf-filter-title"><?php echo esc_html__('Popular Tags', 'elementor-post-filter'); ?></h3>
                        <?php $this->render_tag_filter($settings); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($settings['show_recent_posts'] === 'yes'): ?>
                    <div class="epf-filter-group epf-recent-posts">
                        <h3 class="epf-filter-title"><?php echo esc_html__('Recent posts', 'elementor-post-filter'); ?></h3>
                        <?php $this->render_recent_posts($settings); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="epf-posts-container" 
                 data-post-type="<?php echo esc_attr($settings['post_type']); ?>"
                 data-posts-per-page="<?php echo esc_attr($settings['posts_per_page']); ?>"
                 data-layout="<?php echo esc_attr($settings['layout']); ?>"
                 data-columns="<?php echo esc_attr(isset($settings['columns']) ? $settings['columns'] : 3); ?>"
                 data-pagination-type="<?php echo esc_attr(isset($settings['pagination_type']) ? $settings['pagination_type'] : 'numbers'); ?>"
                 data-load-more-text="<?php echo esc_attr(isset($settings['load_more_text']) ? $settings['load_more_text'] : 'Load More'); ?>"
                 data-loading-text="<?php echo esc_attr(isset($settings['loading_text']) ? $settings['loading_text'] : 'Loading...'); ?>"
                 data-show-thumbnail="<?php echo esc_attr(isset($settings['show_thumbnail']) ? $settings['show_thumbnail'] : 'yes'); ?>"
                 data-show-category-badge="<?php echo esc_attr(isset($settings['show_category_badge']) ? $settings['show_category_badge'] : 'yes'); ?>"
                 data-show-title="<?php echo esc_attr(isset($settings['show_title']) ? $settings['show_title'] : 'yes'); ?>"
                 data-show-excerpt="<?php echo esc_attr(isset($settings['show_excerpt']) ? $settings['show_excerpt'] : 'yes'); ?>"
                 data-show-meta="<?php echo esc_attr(isset($settings['show_meta']) ? $settings['show_meta'] : 'yes'); ?>"
                 data-excerpt-length="<?php echo esc_attr(isset($settings['excerpt_length']) ? $settings['excerpt_length'] : 20); ?>">
                
                <div class="epf-loading" style="display: none;">
                    <span class="epf-loader"></span>
                </div>
                
                <?php $this->render_posts($settings); ?>
            </div>
            
        </div>
        <?php
    }
    
    private function render_category_filter($settings) {
        $categories = get_categories([
            'taxonomy' => 'category',
            'hide_empty' => true,
            'number' => 20
        ]);
        
        if (empty($categories)) {
            return;
        }
        
        echo '<div class="epf-filter-items">';
        
        foreach ($categories as $category) {
            $count_html = '';
            if ($settings['show_post_count'] === 'yes') {
                $count_html = ' <span class="epf-count">' . $category->count . '</span>';
            }
            
            printf(
                '<div class="epf-filter-item" data-filter="category" data-value="%s">%s%s</div>',
                esc_attr($category->slug),
                esc_html($category->name),
                $count_html
            );
        }
        
        echo '</div>';
    }
    
    private function render_tag_filter($settings) {
        $tags = get_tags([
            'taxonomy' => 'post_tag',
            'hide_empty' => true,
            'number' => 20
        ]);
        
        if (empty($tags)) {
            return;
        }
        
        echo '<div class="epf-filter-items epf-tag-items">';
        
        foreach ($tags as $tag) {
            printf(
                '<div class="epf-filter-item epf-tag-item" data-filter="tag" data-value="%s">%s</div>',
                esc_attr($tag->slug),
                esc_html($tag->name)
            );
        }
        
        echo '</div>';
    }
    
    private function render_posts($settings) {
        $args = [
            'post_type' => $settings['post_type'],
            'posts_per_page' => $settings['posts_per_page'],
            'post_status' => 'publish'
        ];
        
        $query = new WP_Query($args);
        
        $layout = isset($settings['layout']) ? $settings['layout'] : 'grid';
        $columns = isset($settings['columns']) ? $settings['columns'] : 3;
        $pagination_type = isset($settings['pagination_type']) ? $settings['pagination_type'] : 'numbers';
        
        if ($query->have_posts()) {
            echo '<div class="epf-posts-wrapper epf-layout-' . esc_attr($layout) . ' epf-columns-' . esc_attr($columns) . '">';
            
            while ($query->have_posts()) {
                $query->the_post();
                $this->render_post_item($settings);
            }
            
            echo '</div>';
            
            // Render pagination based on type
            if ($query->max_num_pages > 1 && $pagination_type !== 'none') {
                if ($pagination_type === 'load_more') {
                    $this->render_load_more_button($settings, $query->max_num_pages);
                } else {
                    echo '<div class="epf-pagination">';
                    echo paginate_links([
                        'total' => $query->max_num_pages,
                        'current' => 1,
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                    ]);
                    echo '</div>';
                }
            }
        } else {
            echo '<p class="epf-no-posts">' . esc_html__('No posts found.', 'elementor-post-filter') . '</p>';
        }
        
        wp_reset_postdata();
    }
    
    private function render_load_more_button($settings, $max_pages) {
        $load_more_text = isset($settings['load_more_text']) ? $settings['load_more_text'] : 'Load More';
        $icon = isset($settings['load_more_icon']) ? $settings['load_more_icon'] : '';
        $icon_position = isset($settings['load_more_icon_position']) ? $settings['load_more_icon_position'] : 'after';
        
        ?>
        <div class="epf-load-more-wrapper" data-max-pages="<?php echo esc_attr($max_pages); ?>" data-current-page="1">
            <button class="epf-load-more-btn" type="button">
                <?php if (!empty($icon['value']) && $icon_position === 'before'): ?>
                    <span class="epf-btn-icon epf-btn-icon-before">
                        <?php \Elementor\Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']); ?>
                    </span>
                <?php endif; ?>
                
                <span class="epf-btn-text"><?php echo esc_html($load_more_text); ?></span>
                
                <?php if (!empty($icon['value']) && $icon_position === 'after'): ?>
                    <span class="epf-btn-icon epf-btn-icon-after">
                        <?php \Elementor\Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']); ?>
                    </span>
                <?php endif; ?>
            </button>
        </div>
        <?php
    }
    
    private function render_post_item($settings) {
        $show_thumbnail = isset($settings['show_thumbnail']) ? $settings['show_thumbnail'] : 'yes';
        $show_category_badge = isset($settings['show_category_badge']) ? $settings['show_category_badge'] : 'yes';
        $show_title = isset($settings['show_title']) ? $settings['show_title'] : 'yes';
        $show_excerpt = isset($settings['show_excerpt']) ? $settings['show_excerpt'] : 'yes';
        $show_meta = isset($settings['show_meta']) ? $settings['show_meta'] : 'yes';
        $excerpt_length = isset($settings['excerpt_length']) ? $settings['excerpt_length'] : 20;
        $layout = isset($settings['layout']) ? $settings['layout'] : 'grid';
        
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
                
                <?php if ($show_excerpt === 'yes' && $layout !== 'minimal'): ?>
                    <div class="epf-post-excerpt">
                        <?php echo wp_trim_words(get_the_excerpt(), $excerpt_length); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($show_meta === 'yes' && $layout !== 'minimal'): ?>
                    <div class="epf-post-meta">
                        <span class="epf-post-date"><?php echo get_the_date(); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }
    
    private function render_recent_posts($settings) {
        $recent_count = isset($settings['recent_posts_count']) ? $settings['recent_posts_count'] : 5;
        $post_type = isset($settings['post_type']) ? $settings['post_type'] : 'post';
        
        $recent_args = [
            'post_type' => $post_type,
            'posts_per_page' => $recent_count,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        ];
        
        $recent_query = new WP_Query($recent_args);
        
        if ($recent_query->have_posts()) {
            echo '<div class="epf-recent-posts-list">';
            
            while ($recent_query->have_posts()) {
                $recent_query->the_post();
                ?>
                <div class="epf-recent-post-item">
                    <?php if (has_post_thumbnail()): ?>
                        <div class="epf-recent-item-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('thumbnail'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="epf-recent-item-content">
                        <a href="<?php the_permalink(); ?>" class="epf-recent-item-title">
                            <?php the_title(); ?>
                        </a>
                    </div>
                </div>
                <?php
            }
            
            echo '</div>';
        }
        
        wp_reset_postdata();
    }
    
    private function get_post_types() {
        $post_types = get_post_types(['public' => true], 'objects');
        $options = [];
        
        foreach ($post_types as $post_type) {
            $options[$post_type->name] = $post_type->label;
        }
        
        return $options;
    }
}