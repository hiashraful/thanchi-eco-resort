<?php
/**
 * Single Post Template
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<?php while (have_posts()) : the_post(); ?>

<!-- Post Header -->
<section class="relative min-h-[60vh] flex items-center -mt-20 pt-40 bg-background-dark">
    <div class="hero-gradient absolute inset-0 bg-background-dark"></div>
    <?php if (has_post_thumbnail()) : ?>
        <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'thanchi-hero')); ?>');"></div>
    <?php endif; ?>
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center w-full">
        <div class="flex items-center justify-center gap-4 text-sm text-[#a9a29a] mb-6">
            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
            <?php if (has_category()) : ?>
                <span>&middot;</span>
                <span class="text-primary"><?php the_category(', '); ?></span>
            <?php endif; ?>
        </div>
        <h1 class="font-serif text-3xl md:text-5xl font-bold text-white mb-6"><?php the_title(); ?></h1>
        <div class="flex items-center justify-center gap-3">
            <?php echo get_avatar(get_the_author_meta('ID'), 40, '', '', array('class' => 'rounded-full')); ?>
            <span class="text-white"><?php the_author(); ?></span>
        </div>
    </div>
</section>

<article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>
    <!-- Post Content -->
    <section class="py-16 bg-background-light dark:bg-background-dark">
        <div class="max-w-3xl mx-auto px-6 lg:px-12">

            <?php if (has_post_thumbnail()) : ?>
                <figure class="mb-12 -mt-24 relative z-20">
                    <?php the_post_thumbnail('large', array(
                        'alt' => esc_attr(get_the_title()),
                        'loading' => 'eager',
                        'class' => 'w-full rounded-2xl shadow-2xl',
                    )); ?>
                    <?php if (get_the_post_thumbnail_caption()) : ?>
                        <figcaption class="text-center text-sm text-[#6b635b] mt-4">
                            <?php echo esc_html(get_the_post_thumbnail_caption()); ?>
                        </figcaption>
                    <?php endif; ?>
                </figure>
            <?php endif; ?>

            <div class="prose prose-lg dark:prose-invert max-w-none
                prose-headings:font-serif prose-headings:font-bold
                prose-h2:text-3xl prose-h2:mt-12 prose-h2:mb-6
                prose-h3:text-2xl prose-h3:mt-8 prose-h3:mb-4
                prose-p:text-[#6b635b] dark:prose-p:text-[#a9a29a] prose-p:leading-relaxed
                prose-a:text-primary prose-a:no-underline hover:prose-a:underline
                prose-strong:text-[#161413] dark:prose-strong:text-white
                prose-ul:text-[#6b635b] dark:prose-ul:text-[#a9a29a]
                prose-ol:text-[#6b635b] dark:prose-ol:text-[#a9a29a]
                prose-blockquote:border-l-primary prose-blockquote:bg-[#f2f1ef] dark:prose-blockquote:bg-[#25211c] prose-blockquote:py-4 prose-blockquote:px-6 prose-blockquote:rounded-r-lg
                prose-img:rounded-2xl prose-img:shadow-lg">
                <?php the_content(); ?>
            </div>

            <?php if (has_tag()) : ?>
                <div class="mt-12 pt-8 border-t border-[#e3e0de] dark:border-[#3a342e]">
                    <div class="flex flex-wrap gap-2">
                        <?php
                        $tags = get_the_tags();
                        if ($tags) :
                            foreach ($tags as $tag) :
                        ?>
                            <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="px-4 py-2 bg-[#f2f1ef] dark:bg-[#25211c] rounded-full text-sm text-[#6b635b] dark:text-[#a9a29a] hover:text-primary transition-colors">
                                #<?php echo esc_html($tag->name); ?>
                            </a>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Author Box -->
            <div class="mt-12 p-8 bg-white dark:bg-[#25211c] rounded-2xl">
                <div class="flex gap-6 items-start">
                    <?php echo get_avatar(get_the_author_meta('ID'), 80, '', '', array('class' => 'rounded-full')); ?>
                    <div>
                        <p class="font-bold text-lg mb-2"><?php esc_html_e('Written by', 'thanchi-eco-resort'); ?> <?php the_author(); ?></p>
                        <p class="text-[#6b635b] dark:text-[#a9a29a]"><?php echo esc_html(get_the_author_meta('description')); ?></p>
                    </div>
                </div>
            </div>

            <!-- Post Navigation -->
            <nav class="mt-12 grid md:grid-cols-2 gap-6" aria-label="<?php esc_attr_e('Post Navigation', 'thanchi-eco-resort'); ?>">
                <?php
                $prev_post = get_previous_post();
                $next_post = get_next_post();
                ?>

                <?php if ($prev_post) : ?>
                    <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" class="p-6 bg-white dark:bg-[#25211c] rounded-xl hover:shadow-lg transition-all group">
                        <span class="text-sm text-[#6b635b] flex items-center gap-2">
                            <span class="material-symbols-outlined">arrow_back</span>
                            <?php esc_html_e('Previous', 'thanchi-eco-resort'); ?>
                        </span>
                        <p class="font-bold mt-2 group-hover:text-primary transition-colors"><?php echo esc_html($prev_post->post_title); ?></p>
                    </a>
                <?php else : ?>
                    <div></div>
                <?php endif; ?>

                <?php if ($next_post) : ?>
                    <a href="<?php echo esc_url(get_permalink($next_post)); ?>" class="p-6 bg-white dark:bg-[#25211c] rounded-xl hover:shadow-lg transition-all group text-right">
                        <span class="text-sm text-[#6b635b] flex items-center justify-end gap-2">
                            <?php esc_html_e('Next', 'thanchi-eco-resort'); ?>
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </span>
                        <p class="font-bold mt-2 group-hover:text-primary transition-colors"><?php echo esc_html($next_post->post_title); ?></p>
                    </a>
                <?php endif; ?>
            </nav>

        </div>
    </section>
</article>

<?php if (comments_open() || get_comments_number()) : ?>
    <section class="py-16 bg-[#f2f1ef] dark:bg-[#25211c]">
        <div class="max-w-3xl mx-auto px-6 lg:px-12">
            <?php comments_template(); ?>
        </div>
    </section>
<?php endif; ?>

<?php endwhile; ?>

<?php
// Related Posts
$related_args = array(
    'post_type' => 'post',
    'posts_per_page' => 3,
    'post__not_in' => array(get_the_ID()),
    'orderby' => 'rand',
);

$categories = get_the_category();
if ($categories) {
    $related_args['category__in'] = array($categories[0]->term_id);
}

$related_posts = new WP_Query($related_args);

if ($related_posts->have_posts()) :
?>
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <h2 class="font-serif text-3xl font-bold text-center mb-12"><?php esc_html_e('More Stories', 'thanchi-eco-resort'); ?></h2>

        <div class="grid md:grid-cols-3 gap-8">
            <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                <article class="bg-white dark:bg-[#25211c] rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group">
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>" class="block h-48 overflow-hidden">
                            <?php the_post_thumbnail('thanchi-card', array(
                                'alt' => esc_attr(get_the_title()),
                                'loading' => 'lazy',
                                'class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-110',
                            )); ?>
                        </a>
                    <?php endif; ?>

                    <div class="p-6">
                        <time class="text-sm text-[#6b635b]" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                        <h3 class="font-serif text-lg font-bold mt-2 group-hover:text-primary transition-colors">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php
wp_reset_postdata();
endif;
?>

<?php get_footer(); ?>
