<?php
/**
 * Search Results Template
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
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center w-full">
        <h1 class="font-serif text-4xl md:text-5xl font-bold text-white">
            <?php
            printf(
                esc_html__('Search Results for: %s', 'thanchi-eco-resort'),
                '<span class="text-primary">' . esc_html(get_search_query()) . '</span>'
            );
            ?>
        </h1>
    </div>
</section>

<!-- Search Results -->
<section class="py-24 bg-background-light dark:bg-background-dark" aria-label="<?php esc_attr_e('Search Results', 'thanchi-eco-resort'); ?>">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">

        <?php if (have_posts()) : ?>

            <p class="text-center text-[#6b635b] dark:text-[#a9a29a] mb-12">
                <?php
                printf(
                    esc_html(_n('Found %d result', 'Found %d results', $wp_query->found_posts, 'thanchi-eco-resort')),
                    $wp_query->found_posts
                );
                ?>
            </p>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('bg-white dark:bg-[#25211c] rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>" class="block h-56 overflow-hidden" aria-hidden="true" tabindex="-1">
                                <?php the_post_thumbnail('thanchi-card', array(
                                    'alt' => esc_attr(get_the_title()),
                                    'loading' => 'lazy',
                                    'class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-110',
                                )); ?>
                            </a>
                        <?php else : ?>
                            <a href="<?php the_permalink(); ?>" class="block h-56 bg-[#f2f1ef] dark:bg-[#1d1915] flex items-center justify-center" aria-hidden="true" tabindex="-1">
                                <span class="material-symbols-outlined text-6xl text-[#e3e0de] dark:text-[#3a342e]">article</span>
                            </a>
                        <?php endif; ?>

                        <div class="p-6">
                            <div class="flex items-center gap-4 text-sm text-[#6b635b] dark:text-[#a9a29a] mb-4">
                                <span><?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name); ?></span>
                                <span>&middot;</span>
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                            </div>

                            <h2 class="font-serif text-xl font-bold mb-3 group-hover:text-primary transition-colors">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <p class="text-sm text-[#6b635b] dark:text-[#a9a29a] line-clamp-3"><?php echo esc_html(get_the_excerpt()); ?></p>
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
                <nav class="mt-16 flex justify-center gap-2" role="navigation" aria-label="<?php esc_attr_e('Search Results Pagination', 'thanchi-eco-resort'); ?>">
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
                <span class="material-symbols-outlined text-6xl text-[#e3e0de] dark:text-[#3a342e] mb-6 block">search_off</span>
                <h2 class="font-serif text-3xl font-bold mb-4"><?php esc_html_e('Nothing Found', 'thanchi-eco-resort'); ?></h2>
                <p class="text-[#6b635b] dark:text-[#a9a29a] mb-8 max-w-md mx-auto">
                    <?php esc_html_e('Sorry, we could not find anything matching your search. Please try different keywords.', 'thanchi-eco-resort'); ?>
                </p>

                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="max-w-md mx-auto mb-12">
                    <label for="search-retry" class="screen-reader-text"><?php esc_html_e('Search for:', 'thanchi-eco-resort'); ?></label>
                    <div class="flex gap-2">
                        <input type="search" id="search-retry" name="s" value="<?php echo esc_attr(get_search_query()); ?>"
                            placeholder="<?php esc_attr_e('Search...', 'thanchi-eco-resort'); ?>"
                            class="flex-1 px-4 py-3 border border-[#e3e0de] dark:border-[#3a342e] rounded-lg bg-white dark:bg-background-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        <button type="submit" class="bg-primary hover:bg-[#855935] text-white px-6 py-3 rounded-lg font-bold transition-all">
                            <?php esc_html_e('Search', 'thanchi-eco-resort'); ?>
                        </button>
                    </div>
                </form>

                <p class="text-[#6b635b] dark:text-[#a9a29a] mb-4"><?php esc_html_e('Or explore our popular pages:', 'thanchi-eco-resort'); ?></p>
                <div class="flex gap-4 justify-center flex-wrap">
                    <a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="bg-white/10 backdrop-blur-md border border-[#e3e0de] dark:border-[#3a342e] px-6 py-2.5 rounded-lg font-bold hover:text-primary transition-all"><?php esc_html_e('Rooms', 'thanchi-eco-resort'); ?></a>
                    <a href="<?php echo esc_url(home_url('/restaurant/')); ?>" class="bg-white/10 backdrop-blur-md border border-[#e3e0de] dark:border-[#3a342e] px-6 py-2.5 rounded-lg font-bold hover:text-primary transition-all"><?php esc_html_e('Restaurant', 'thanchi-eco-resort'); ?></a>
                    <a href="<?php echo esc_url(home_url('/about/')); ?>" class="bg-white/10 backdrop-blur-md border border-[#e3e0de] dark:border-[#3a342e] px-6 py-2.5 rounded-lg font-bold hover:text-primary transition-all"><?php esc_html_e('About', 'thanchi-eco-resort'); ?></a>
                </div>
            </div>

        <?php endif; ?>

    </div>
</section>

<?php get_footer(); ?>
