<?php
/**
 * 404 Error Page Template
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<!-- 404 Section -->
<section class="relative min-h-[80vh] flex items-center -mt-20 pt-40 bg-background-dark">
    <div class="hero-gradient absolute inset-0 bg-background-dark"></div>
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center w-full" aria-labelledby="error-title">
        <p class="text-[120px] md:text-[180px] font-serif font-bold text-primary/30 leading-none mb-0" aria-hidden="true">404</p>
        <h1 id="error-title" class="font-serif text-4xl md:text-5xl font-bold text-white mb-6 -mt-8"><?php esc_html_e('Page Not Found', 'thanchi-eco-resort'); ?></h1>
        <p class="text-lg text-[#a9a29a] max-w-2xl mx-auto mb-10">
            <?php esc_html_e('Like many paths in the hills of Thanchi, this one seems to have disappeared. The page you are looking for does not exist or has been moved.', 'thanchi-eco-resort'); ?>
        </p>

        <div class="flex flex-wrap gap-4 justify-center mb-16">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="bg-primary hover:bg-[#855935] text-white px-8 py-3 rounded-lg font-bold transition-all">
                <?php esc_html_e('Back to Home', 'thanchi-eco-resort'); ?>
            </a>
            <a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-8 py-3 rounded-lg font-bold hover:bg-white/20 transition-all">
                <?php esc_html_e('View Rooms', 'thanchi-eco-resort'); ?>
            </a>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-8 py-3 rounded-lg font-bold hover:bg-white/20 transition-all">
                <?php esc_html_e('Contact Us', 'thanchi-eco-resort'); ?>
            </a>
        </div>

        <div class="pt-12 border-t border-[#3a342e] max-w-md mx-auto">
            <h2 class="text-lg font-bold text-white mb-6"><?php esc_html_e('Popular Pages', 'thanchi-eco-resort'); ?></h2>
            <ul class="space-y-3 text-sm">
                <li><a href="<?php echo esc_url(home_url('/')); ?>" class="text-[#a9a29a] hover:text-primary transition-colors"><?php esc_html_e('Home - Thanchi Eco Resort', 'thanchi-eco-resort'); ?></a></li>
                <li><a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="text-[#a9a29a] hover:text-primary transition-colors"><?php esc_html_e('Rooms & Booking', 'thanchi-eco-resort'); ?></a></li>
                <li><a href="<?php echo esc_url(home_url('/restaurant/')); ?>" class="text-[#a9a29a] hover:text-primary transition-colors"><?php esc_html_e('Restaurant Menu', 'thanchi-eco-resort'); ?></a></li>
                <li><a href="<?php echo esc_url(home_url('/about/')); ?>" class="text-[#a9a29a] hover:text-primary transition-colors"><?php esc_html_e('About Us', 'thanchi-eco-resort'); ?></a></li>
                <li><a href="<?php echo esc_url(home_url('/contact/')); ?>" class="text-[#a9a29a] hover:text-primary transition-colors"><?php esc_html_e('Contact', 'thanchi-eco-resort'); ?></a></li>
            </ul>
        </div>
    </div>
</section>

<?php get_footer(); ?>
