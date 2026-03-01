<?php
/**
 * Sidebar Template
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="widget-area" role="complementary" aria-label="<?php esc_attr_e('Sidebar', 'thanchi-eco-resort'); ?>">
    <?php dynamic_sidebar('sidebar-1'); ?>
</aside>
