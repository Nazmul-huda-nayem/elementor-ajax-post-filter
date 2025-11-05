(function($) {
    'use strict';

    class PostFilter {
        constructor(element) {
            this.$wrapper = $(element);
            this.$container = this.$wrapper.find('.epf-posts-container');
            this.$loading = this.$wrapper.find('.epf-loading');
            this.filters = {
                category: '',
                tag: ''
            };
            this.paged = 1;
            this.isInitialized = false;
            
            this.init();
        }

        init() {
            if (this.isInitialized) {
                return;
            }
            this.isInitialized = true;
            this.bindEvents();
        }

        bindEvents() {
            const self = this;

            // Unbind first to prevent multiple bindings
            this.$wrapper.off('click.epf');

            // Category filter click
            this.$wrapper.on('click.epf', '.epf-category-filter .epf-filter-item', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const $item = $(this);
                const value = $item.data('value');

                // Toggle active state
                if ($item.hasClass('active')) {
                    $item.removeClass('active');
                    self.filters.category = '';
                } else {
                    $item.siblings().removeClass('active');
                    $item.addClass('active');
                    self.filters.category = value;
                }

                self.paged = 1;
                self.loadPosts();
            });

            // Tag filter click
            this.$wrapper.on('click.epf', '.epf-tag-filter .epf-filter-item', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const $item = $(this);
                const value = $item.data('value');

                // Toggle active state
                if ($item.hasClass('active')) {
                    $item.removeClass('active');
                    self.filters.tag = '';
                } else {
                    $item.siblings().removeClass('active');
                    $item.addClass('active');
                    self.filters.tag = value;
                }

                self.paged = 1;
                self.loadPosts();
            });

            // Pagination click - use event delegation for dynamically loaded content
            this.$wrapper.on('click.epf', '.epf-pagination a.page-numbers', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const $link = $(this);
                
                if ($link.hasClass('current') || $link.hasClass('dots')) {
                    return;
                }

                // Extract page number from URL
                const href = $link.attr('href');
                const pageMatch = href.match(/[?&]paged=(\d+)/);
                
                if (pageMatch) {
                    self.paged = parseInt(pageMatch[1]);
                } else if ($link.hasClass('next')) {
                    self.paged++;
                } else if ($link.hasClass('prev')) {
                    self.paged--;
                } else {
                    self.paged = parseInt($link.text()) || 1;
                }

                self.loadPosts();
                
                // Scroll to top of posts
                $('html, body').animate({
                    scrollTop: self.$wrapper.offset().top - 100
                }, 500);
            });
            
            // Load More button click
            this.$wrapper.on('click.epf', '.epf-load-more-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const $btn = $(this);
                const $wrapper = $btn.closest('.epf-load-more-wrapper');
                const currentPage = parseInt($wrapper.data('current-page')) || 1;
                const maxPages = parseInt($wrapper.data('max-pages')) || 1;
                
                if (currentPage >= maxPages || $btn.hasClass('loading')) {
                    return;
                }
                
                self.loadMorePosts($btn, currentPage + 1);
            });

            console.log('EPF: Filter events bound successfully');
        }
        
        loadMorePosts($btn, page) {
            const self = this;
            const $btnWrapper = $btn.closest('.epf-load-more-wrapper');
            const originalText = $btn.find('.epf-btn-text').text();
            const loadingText = this.$container.data('loading-text') || 'Loading...';
            
            // Update button state
            $btn.addClass('loading').prop('disabled', true);
            $btn.find('.epf-btn-text').text(loadingText);
            
            // Get settings
            const postType = this.$container.data('post-type') || 'post';
            const postsPerPage = this.$container.data('posts-per-page') || 6;
            const layout = this.$container.data('layout') || 'grid';
            const columns = this.$container.data('columns') || 3;
            const paginationType = this.$container.data('pagination-type') || 'numbers';
            const loadMoreText = this.$container.data('load-more-text') || 'Load More';
            
            // Get visibility settings
            const showThumbnail = this.$container.data('show-thumbnail') || 'yes';
            const showCategoryBadge = this.$container.data('show-category-badge') || 'yes';
            const showTitle = this.$container.data('show-title') || 'yes';
            const showExcerpt = this.$container.data('show-excerpt') || 'yes';
            const showMeta = this.$container.data('show-meta') || 'yes';
            const excerptLength = this.$container.data('excerpt-length') || 20;
            
            $.ajax({
                url: epfAjax.ajax_url,
                type: 'POST',
                data: {
                    action: 'epf_filter_posts',
                    nonce: epfAjax.nonce,
                    category: this.filters.category,
                    tag: this.filters.tag,
                    post_type: postType,
                    posts_per_page: postsPerPage,
                    paged: page,
                    layout: layout,
                    columns: columns,
                    pagination_type: paginationType,
                    load_more_text: loadMoreText,
                    is_load_more: true,
                    show_thumbnail: showThumbnail,
                    show_category_badge: showCategoryBadge,
                    show_title: showTitle,
                    show_excerpt: showExcerpt,
                    show_meta: showMeta,
                    excerpt_length: excerptLength
                },
                success: function(response) {
                    if (response.success && response.html) {
                        // Append new posts to wrapper
                        const $postsWrapper = self.$container.find('.epf-posts-wrapper');
                        $(response.html).hide().appendTo($postsWrapper).fadeIn(400);
                        
                        // Update current page
                        $btnWrapper.data('current-page', page);
                        
                        // Hide button if no more pages
                        if (page >= response.max_pages) {
                            $btnWrapper.fadeOut(300);
                        }
                        
                        // Reset button state
                        $btn.removeClass('loading').prop('disabled', false);
                        $btn.find('.epf-btn-text').text(originalText);
                    } else {
                        console.error('EPF: Error loading more posts', response);
                        $btn.removeClass('loading').prop('disabled', false);
                        $btn.find('.epf-btn-text').text(originalText);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('EPF: AJAX Error', {xhr: xhr, status: status, error: error});
                    $btn.removeClass('loading').prop('disabled', false);
                    $btn.find('.epf-btn-text').text(originalText);
                    alert('Error loading more posts. Please try again.');
                }
            });
        }

       loadPosts() {
    const self = this;
    
    console.log('EPF: Loading posts with filters', this.filters);
    
    // Show loading
    this.$loading.fadeIn(200);

    // Get settings from container
    const postType = this.$container.data('post-type') || 'post';
    const postsPerPage = this.$container.data('posts-per-page') || 6;
    const layout = this.$container.data('layout') || 'grid';
    const columns = this.$container.data('columns') || 3;
    const paginationType = this.$container.data('pagination-type') || 'numbers';
    const loadMoreText = this.$container.data('load-more-text') || 'Load More';
    
    // Get visibility settings from container
    const showThumbnail = this.$container.data('show-thumbnail') || 'yes';
    const showCategoryBadge = this.$container.data('show-category-badge') || 'yes';
    const showTitle = this.$container.data('show-title') || 'yes';
    const showExcerpt = this.$container.data('show-excerpt') || 'yes';
    const showMeta = this.$container.data('show-meta') || 'yes';
    const excerptLength = this.$container.data('excerpt-length') || 20;

    // Check if ajax variables are available
    if (typeof epfAjax === 'undefined') {
        console.error('EPF: Ajax variables not loaded');
        this.$loading.fadeOut(200);
        return;
    }

    $.ajax({
        url: epfAjax.ajax_url,
        type: 'POST',
        data: {
            action: 'epf_filter_posts',
            nonce: epfAjax.nonce,
            category: this.filters.category,
            tag: this.filters.tag,
            post_type: postType,
            posts_per_page: postsPerPage,
            paged: this.paged,
            layout: layout,
            columns: columns,
            pagination_type: paginationType,
            load_more_text: loadMoreText,
            is_load_more: false,
            show_thumbnail: showThumbnail,
            show_category_badge: showCategoryBadge,
            show_title: showTitle,
            show_excerpt: showExcerpt,
            show_meta: showMeta,
            excerpt_length: excerptLength
        },
        success: function(response) {
            console.log('EPF: AJAX response received', response);
            
            if (response.success) {
                // Remove all existing content except loading indicator
                self.$container.children(':not(.epf-loading)').remove();
                
                // Insert new posts content
                $(response.html).hide().appendTo(self.$container).fadeIn(300);
                
                // Insert pagination if exists
                if (response.pagination) {
                    $(response.pagination).hide().appendTo(self.$container).fadeIn(300);
                }
                
                // Hide loading
                self.$loading.fadeOut(200);
            } else {
                console.error('EPF: Error in response', response);
                self.$loading.fadeOut(200);
            }
        },
        error: function(xhr, status, error) {
            console.error('EPF: AJAX Error', {xhr: xhr, status: status, error: error});
            self.$loading.fadeOut(200);
            
            // Show error message to user
            alert('Error loading posts. Please try again.');
        }
    });
}
    }

    // Store initialized instances
    var instances = [];

    function initPostFilter($element) {
        // Check if already initialized
        if ($element.data('epf-initialized')) {
            console.log('EPF: Already initialized, skipping');
            return;
        }
        
        console.log('EPF: Initializing post filter');
        $element.data('epf-initialized', true);
        
        var instance = new PostFilter($element[0]);
        instances.push(instance);
    }

    // Initialize on Elementor frontend
    $(window).on('elementor/frontend/init', function() {
        console.log('EPF: Elementor frontend init');
        
        elementorFrontend.hooks.addAction('frontend/element_ready/post_filter.default', function($scope) {
            console.log('EPF: Widget ready in Elementor');
            var $wrapper = $scope.find('.epf-post-filter-wrapper');
            if ($wrapper.length) {
                initPostFilter($wrapper);
            }
        });
    });

    // Initialize on regular page load (for non-Elementor pages and frontend)
    $(document).ready(function() {
        console.log('EPF: Document ready, searching for widgets');
        
        // Wait a bit for Elementor to finish loading
        setTimeout(function() {
            $('.epf-post-filter-wrapper').each(function() {
                var $this = $(this);
                if (!$this.data('epf-initialized')) {
                    console.log('EPF: Found uninitialized widget');
                    initPostFilter($this);
                }
            });
        }, 500);
    });

    // Also try on window load as backup
    $(window).on('load', function() {
        console.log('EPF: Window loaded, checking for widgets');
        
        $('.epf-post-filter-wrapper').each(function() {
            var $this = $(this);
            if (!$this.data('epf-initialized')) {
                console.log('EPF: Initializing widget on window load');
                initPostFilter($this);
            }
        });
    });

})(jQuery);