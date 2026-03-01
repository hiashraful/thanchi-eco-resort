<?php
/**
 * Template Name: About Page
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$people = thanchi_get_people();
?>

<!-- Page Header -->
<section class="relative py-32 pt-40 -mt-20 bg-background-dark">
    <div class="hero-gradient absolute inset-0 bg-background-dark"></div>
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('<?php echo esc_url(THANCHI_URI . '/assets/images/about-story-placeholder.jpg'); ?>');"></div>
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center">
        <span class="text-primary font-bold tracking-widest text-sm uppercase mb-4 block"><?php esc_html_e('Our Story', 'thanchi-eco-resort'); ?></span>
        <h1 class="font-serif text-4xl md:text-6xl font-bold text-white mb-6"><?php esc_html_e('Three Friends. One Dream.', 'thanchi-eco-resort'); ?></h1>
        <p class="text-lg text-[#a9a29a] max-w-2xl mx-auto">
            <?php esc_html_e('A wooden stay in the hills of Thanchi, Bandarban. Built with our hands, rooted in this land.', 'thanchi-eco-resort'); ?>
        </p>
    </div>
</section>

<!-- How It All Began -->
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="relative rounded-2xl overflow-hidden aspect-[4/3] shadow-2xl">
                <img
                    src="<?php echo esc_url(THANCHI_URI . '/assets/images/about-story-placeholder.jpg'); ?>"
                    alt="<?php esc_attr_e('The three founders of Thanchi Eco Resort standing together in the hills', 'thanchi-eco-resort'); ?>"
                    class="w-full h-full object-cover"
                    loading="eager"
                >
                <div class="absolute inset-0 bg-primary/10 mix-blend-multiply"></div>
            </div>
            <div>
                <span class="text-primary font-bold tracking-widest text-sm uppercase mb-4 block"><?php esc_html_e('The Beginning', 'thanchi-eco-resort'); ?></span>
                <h2 class="font-serif text-3xl md:text-4xl font-bold mb-6"><?php esc_html_e('How It All Began', 'thanchi-eco-resort'); ?></h2>
                <div class="space-y-4 text-[#7f756c] dark:text-[#a9a29a] leading-relaxed">
                    <p><?php esc_html_e('We grew up in Thanchi. We know every trail, every river bend, every sunrise spot. When friends from the city visited, they would always say: "This place is incredible. Why is there nowhere to stay?"', 'thanchi-eco-resort'); ?></p>
                    <p><?php esc_html_e('That question stayed with us. We could build luxury resorts that would destroy what makes Thanchi special. Or we could build something different - a place that is honest to this land.', 'thanchi-eco-resort'); ?></p>
                    <p><?php esc_html_e('In 2019, we started building. Bamboo from the hills around us. Wood from fallen trees. Skills from our fathers and grandfathers. No architects. No contractors. Just three friends and a vision.', 'thanchi-eco-resort'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Meet the Founders -->
<section class="py-24 bg-[#f2f1ef] dark:bg-[#25211c]">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="text-center mb-16">
            <h2 class="font-serif text-4xl font-bold mb-4"><?php esc_html_e('Meet the People Behind This Place', 'thanchi-eco-resort'); ?></h2>
            <p class="text-[#7f756c] dark:text-[#a9a29a]"><?php esc_html_e('No corporate team. No management hierarchy. Just three friends who love these hills.', 'thanchi-eco-resort'); ?></p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($people as $index => $person) : ?>
                <article class="bg-white dark:bg-background-dark rounded-2xl overflow-hidden shadow-md text-center p-8">
                    <div class="w-32 h-32 mx-auto rounded-full overflow-hidden mb-6 border-4 border-primary/20">
                        <img
                            src="<?php echo esc_url(THANCHI_URI . '/assets/images/founder-' . ($index + 1) . '-placeholder.jpg'); ?>"
                            alt="<?php echo esc_attr($person['name']); ?>"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        >
                    </div>
                    <h3 class="font-serif text-xl font-bold mb-1"><?php echo esc_html($person['name']); ?></h3>
                    <p class="text-primary font-bold text-sm mb-4"><?php echo esc_html($person['role']); ?></p>
                    <p class="text-[#7f756c] dark:text-[#a9a29a] text-sm leading-relaxed"><?php echo esc_html($person['description']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- What We Believe -->
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="text-center mb-16">
            <h2 class="font-serif text-4xl font-bold mb-4"><?php esc_html_e('What We Believe', 'thanchi-eco-resort'); ?></h2>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="p-8 bg-white dark:bg-[#25211c] border border-[#e3e0de] dark:border-[#3a342e] rounded-2xl text-center">
                <span class="material-symbols-outlined text-primary text-4xl mb-4 block">eco</span>
                <h3 class="font-bold text-lg mb-3"><?php esc_html_e('Eco First', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#7f756c] dark:text-[#a9a29a]"><?php esc_html_e('We use solar power. We collect rainwater. We compost waste. The hills gave us everything - we try to give back.', 'thanchi-eco-resort'); ?></p>
            </div>

            <div class="p-8 bg-white dark:bg-[#25211c] border border-[#e3e0de] dark:border-[#3a342e] rounded-2xl text-center">
                <span class="material-symbols-outlined text-primary text-4xl mb-4 block">groups</span>
                <h3 class="font-bold text-lg mb-3"><?php esc_html_e('Community', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#7f756c] dark:text-[#a9a29a]"><?php esc_html_e('All our staff are from Thanchi. We buy from local farmers. Every rupee you spend here stays in this community.', 'thanchi-eco-resort'); ?></p>
            </div>

            <div class="p-8 bg-white dark:bg-[#25211c] border border-[#e3e0de] dark:border-[#3a342e] rounded-2xl text-center">
                <span class="material-symbols-outlined text-primary text-4xl mb-4 block">cottage</span>
                <h3 class="font-bold text-lg mb-3"><?php esc_html_e('Simplicity', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#7f756c] dark:text-[#a9a29a]"><?php esc_html_e('We could add more. Install AC. Get generators. But then this would become just another resort. We choose to stay simple.', 'thanchi-eco-resort'); ?></p>
            </div>

            <div class="p-8 bg-white dark:bg-[#25211c] border border-[#e3e0de] dark:border-[#3a342e] rounded-2xl text-center">
                <span class="material-symbols-outlined text-primary text-4xl mb-4 block">handshake</span>
                <h3 class="font-bold text-lg mb-3"><?php esc_html_e('Honesty', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#7f756c] dark:text-[#a9a29a]"><?php esc_html_e('We tell you exactly what we have and what we do not. No surprises. No disappointments. Just reality, beautiful as it is.', 'thanchi-eco-resort'); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Timeline / Our Journey -->
<section class="py-24 bg-[#f2f1ef] dark:bg-[#25211c]">
    <div class="max-w-3xl mx-auto px-6 lg:px-12">
        <div class="text-center mb-16">
            <h2 class="font-serif text-4xl font-bold mb-4"><?php esc_html_e('Our Journey', 'thanchi-eco-resort'); ?></h2>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-background-dark p-6 rounded-xl border-l-4 border-primary">
                <p class="text-primary font-bold text-sm mb-1">2019</p>
                <h3 class="font-bold text-lg mb-2"><?php esc_html_e('The Beginning', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#7f756c] dark:text-[#a9a29a]"><?php esc_html_e('Three friends start construction with bamboo, wood, and a dream. First structure completed in 6 months.', 'thanchi-eco-resort'); ?></p>
            </div>

            <div class="bg-white dark:bg-background-dark p-6 rounded-xl border-l-4 border-primary">
                <p class="text-primary font-bold text-sm mb-1">2020</p>
                <h3 class="font-bold text-lg mb-2"><?php esc_html_e('First Guests', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#7f756c] dark:text-[#a9a29a]"><?php esc_html_e('Despite the pandemic, our first guests arrived - a group of photographers looking for untouched nature. They stayed for two weeks.', 'thanchi-eco-resort'); ?></p>
            </div>

            <div class="bg-white dark:bg-background-dark p-6 rounded-xl border-l-4 border-primary">
                <p class="text-primary font-bold text-sm mb-1">2021</p>
                <h3 class="font-bold text-lg mb-2"><?php esc_html_e('Growing Slowly', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#7f756c] dark:text-[#a9a29a]"><?php esc_html_e('Added two more rooms and started our restaurant. Shoriful officially became our full-time cook.', 'thanchi-eco-resort'); ?></p>
            </div>

            <div class="bg-white dark:bg-background-dark p-6 rounded-xl border-l-4 border-primary">
                <p class="text-primary font-bold text-sm mb-1">2022</p>
                <h3 class="font-bold text-lg mb-2"><?php esc_html_e('Word Spreads', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#7f756c] dark:text-[#a9a29a]"><?php esc_html_e('Featured in travel blogs. International guests start arriving. Still no WiFi. Still no AC. Still honest.', 'thanchi-eco-resort'); ?></p>
            </div>

            <div class="bg-white dark:bg-background-dark p-6 rounded-xl border-l-4 border-green-600">
                <p class="text-green-600 font-bold text-sm mb-1"><?php esc_html_e('Today', 'thanchi-eco-resort'); ?></p>
                <h3 class="font-bold text-lg mb-2"><?php esc_html_e('Your Turn', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#7f756c] dark:text-[#a9a29a]"><?php esc_html_e('Come be part of our story. Every guest who leaves renewed adds to what this place means.', 'thanchi-eco-resort'); ?></p>
            </div>
        </div>

        <div class="text-center mt-12">
            <a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="inline-block bg-primary hover:bg-[#855935] text-white px-10 py-4 rounded-lg font-bold transition-all">
                <?php esc_html_e('Book Your Stay', 'thanchi-eco-resort'); ?>
            </a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
