<?php
/**
 * Footer template
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

</main><!-- #main-content -->

<!-- Footer -->
<footer class="bg-background-dark text-[#a9a29a] py-16 px-6 lg:px-12 border-t border-[#3a342e]" role="contentinfo">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
            <!-- Brand Column -->
            <div class="col-span-1 lg:col-span-1">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-3 mb-6">
                    <div class="size-8 bg-primary rounded flex items-center justify-center text-white">
                        <span class="material-symbols-outlined text-lg">nature_people</span>
                    </div>
                    <h2 class="text-lg font-serif font-bold tracking-tight text-white"><?php bloginfo('name'); ?></h2>
                </a>
                <p class="text-sm leading-relaxed">
                    <?php esc_html_e('Preserving the raw beauty of Bandarban through sustainable tourism and indigenous-inspired hospitality.', 'thanchi-eco-resort'); ?>
                </p>
            </div>

            <!-- Explore Column -->
            <div>
                <h3 class="text-white font-bold mb-6"><?php esc_html_e('Explore', 'thanchi-eco-resort'); ?></h3>
                <ul class="space-y-3 text-sm">
                    <li><a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="hover:text-primary transition-colors"><?php esc_html_e('Our Rooms', 'thanchi-eco-resort'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/restaurant/')); ?>" class="hover:text-primary transition-colors"><?php esc_html_e('The Restaurant', 'thanchi-eco-resort'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/about/')); ?>" class="hover:text-primary transition-colors"><?php esc_html_e('Experiences', 'thanchi-eco-resort'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/shop/')); ?>" class="hover:text-primary transition-colors"><?php esc_html_e('The Eco Shop', 'thanchi-eco-resort'); ?></a></li>
                </ul>
            </div>

            <!-- About Column -->
            <div>
                <h3 class="text-white font-bold mb-6"><?php esc_html_e('About', 'thanchi-eco-resort'); ?></h3>
                <ul class="space-y-3 text-sm">
                    <li><a href="<?php echo esc_url(home_url('/about/')); ?>" class="hover:text-primary transition-colors"><?php esc_html_e('Our Story', 'thanchi-eco-resort'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/about/')); ?>" class="hover:text-primary transition-colors"><?php esc_html_e('Sustainability', 'thanchi-eco-resort'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/contact/')); ?>" class="hover:text-primary transition-colors"><?php esc_html_e('Careers', 'thanchi-eco-resort'); ?></a></li>
                    <li><a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="hover:text-primary transition-colors"><?php esc_html_e('Blog', 'thanchi-eco-resort'); ?></a></li>
                </ul>
            </div>

            <!-- Newsletter Column -->
            <div>
                <h3 class="text-white font-bold mb-6"><?php esc_html_e('Newsletter', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm mb-4"><?php esc_html_e('Updates on nature treks and seasonal menus.', 'thanchi-eco-resort'); ?></p>
                <form class="flex gap-2" action="#" method="post">
                    <input type="email" name="email" class="bg-[#2a241f] border-none rounded-lg px-4 py-2 text-sm w-full focus:ring-1 focus:ring-primary text-white placeholder-[#7f756c]" placeholder="<?php esc_attr_e('Your email', 'thanchi-eco-resort'); ?>" required>
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-[#855935] transition-colors" aria-label="<?php esc_attr_e('Subscribe', 'thanchi-eco-resort'); ?>">
                        <span class="material-symbols-outlined text-sm">send</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="pt-8 border-t border-[#3a342e] flex flex-col md:flex-row justify-between items-center gap-4 text-xs">
            <p>&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('All rights reserved.', 'thanchi-eco-resort'); ?></p>
            <div class="flex gap-6">
                <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" class="hover:text-white transition-colors"><?php esc_html_e('Privacy Policy', 'thanchi-eco-resort'); ?></a>
                <a href="<?php echo esc_url(home_url('/terms/')); ?>" class="hover:text-white transition-colors"><?php esc_html_e('Terms of Service', 'thanchi-eco-resort'); ?></a>
                <a href="<?php echo esc_url(home_url('/cookies/')); ?>" class="hover:text-white transition-colors"><?php esc_html_e('Cookie Policy', 'thanchi-eco-resort'); ?></a>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

</body>
</html>
