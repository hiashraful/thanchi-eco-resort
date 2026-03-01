<?php
/**
 * Blog Index Template
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<!-- Page Header -->
<section class="relative min-h-[60vh] flex items-center -mt-20 pt-40 bg-background-dark">
    <div class="hero-gradient absolute inset-0 bg-background-dark"></div>
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('<?php echo esc_url(THANCHI_URI . '/assets/images/blog-header.jpg'); ?>');"></div>
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center w-full">
        <span class="text-primary font-bold tracking-widest text-sm uppercase mb-4 block"><?php esc_html_e('Stories & Guides', 'thanchi-eco-resort'); ?></span>
        <h1 class="font-serif text-4xl md:text-6xl font-bold text-white mb-6"><?php esc_html_e('Travel Stories & Tips', 'thanchi-eco-resort'); ?></h1>
        <p class="text-lg text-[#a9a29a] max-w-2xl mx-auto">
            <?php esc_html_e('Stories from Thanchi, travel tips for Bandarban, and insights into hill life.', 'thanchi-eco-resort'); ?>
        </p>
    </div>
</section>

<!-- Blog Posts -->
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">

        <?php if (have_posts()) : ?>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('bg-white dark:bg-[#25211c] rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>" class="block h-56 overflow-hidden">
                                <?php the_post_thumbnail('thanchi-card', array(
                                    'alt' => esc_attr(get_the_title()),
                                    'loading' => 'lazy',
                                    'class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-110',
                                )); ?>
                            </a>
                        <?php else : ?>
                            <a href="<?php the_permalink(); ?>" class="block h-56 bg-[#f2f1ef] dark:bg-[#1d1915] flex items-center justify-center">
                                <span class="material-symbols-outlined text-6xl text-[#e3e0de] dark:text-[#3a342e]">article</span>
                            </a>
                        <?php endif; ?>

                        <div class="p-6">
                            <div class="flex items-center gap-4 text-sm text-[#6b635b] dark:text-[#a9a29a] mb-4">
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                                <?php if (has_category()) : ?>
                                    <span>&middot;</span>
                                    <?php the_category(', '); ?>
                                <?php endif; ?>
                            </div>

                            <h2 class="font-serif text-xl font-bold mb-3 group-hover:text-primary transition-colors">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <p class="text-sm text-[#6b635b] dark:text-[#a9a29a] line-clamp-3"><?php echo esc_html(get_the_excerpt()); ?></p>

                            <a href="<?php the_permalink(); ?>" class="inline-flex items-center gap-2 text-primary font-bold text-sm mt-4 hover:underline">
                                <?php esc_html_e('Read More', 'thanchi-eco-resort'); ?>
                                <span class="material-symbols-outlined text-lg">arrow_forward</span>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php
            $pagination = paginate_links(array(
                'prev_text' => '<span class="material-symbols-outlined">chevron_left</span>',
                'next_text' => '<span class="material-symbols-outlined">chevron_right</span>',
                'type' => 'array',
            ));

            if ($pagination) :
            ?>
                <nav class="mt-16 flex justify-center gap-2" role="navigation" aria-label="<?php esc_attr_e('Blog Pagination', 'thanchi-eco-resort'); ?>">
                    <?php foreach ($pagination as $page) : ?>
                        <?php echo str_replace(
                            array('page-numbers', 'current'),
                            array('inline-flex items-center justify-center min-w-[44px] h-11 px-4 rounded-lg font-medium transition-colors', 'bg-primary text-white'),
                            $page
                        ); ?>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>

        <?php else : ?>

            <div class="text-center py-16">
                <span class="material-symbols-outlined text-6xl text-[#e3e0de] dark:text-[#3a342e] mb-6 block">edit_note</span>
                <h2 class="font-serif text-3xl font-bold mb-4"><?php esc_html_e('No Posts Yet', 'thanchi-eco-resort'); ?></h2>
                <p class="text-[#6b635b] dark:text-[#a9a29a] mb-8 max-w-md mx-auto">
                    <?php esc_html_e('We are working on sharing stories from Thanchi. Check back soon.', 'thanchi-eco-resort'); ?>
                </p>

                <div class="max-w-md mx-auto text-left bg-white dark:bg-[#25211c] rounded-xl p-6">
                    <h3 class="font-bold mb-4"><?php esc_html_e('Coming Soon:', 'thanchi-eco-resort'); ?></h3>
                    <ul class="space-y-3 text-[#6b635b] dark:text-[#a9a29a]">
                        <li class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">schedule</span>
                            <?php esc_html_e('How to Reach Thanchi from Bandarban', 'thanchi-eco-resort'); ?>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">schedule</span>
                            <?php esc_html_e('What to Expect When You Stay at Thanchi Eco Resort', 'thanchi-eco-resort'); ?>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">schedule</span>
                            <?php esc_html_e('A Day Without Network in Thanchi', 'thanchi-eco-resort'); ?>
                        </li>
                    </ul>
                </div>
            </div>

        <?php endif; ?>

    </div>
</section>

<?php get_footer(); ?>
