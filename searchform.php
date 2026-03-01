<?php
/**
 * Search Form Template
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

$unique_id = wp_unique_id('search-form-');
?>

<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
    <label for="<?php echo esc_attr($unique_id); ?>">
        <span class="screen-reader-text"><?php echo esc_html_x('Search for:', 'label', 'thanchi-eco-resort'); ?></span>
    </label>
    <div class="flex gap-2">
        <input
            type="search"
            id="<?php echo esc_attr($unique_id); ?>"
            placeholder="<?php echo esc_attr_x('Search...', 'placeholder', 'thanchi-eco-resort'); ?>"
            value="<?php echo get_search_query(); ?>"
            name="s"
            class="flex-1 px-4 py-3 border border-[#e3e0de] dark:border-[#3a342e] rounded-lg bg-white dark:bg-background-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
        >
        <button type="submit" class="bg-primary hover:bg-[#855935] text-white px-4 py-3 rounded-lg transition-all" aria-label="<?php echo esc_attr_x('Search', 'submit button', 'thanchi-eco-resort'); ?>">
            <span class="material-symbols-outlined">search</span>
        </button>
    </div>
</form>
