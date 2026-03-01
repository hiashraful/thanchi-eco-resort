<?php
/**
 * Default Page Template
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<?php while (have_posts()) : the_post(); ?>

<!-- Page Header -->
<section class="relative py-32 pt-40 -mt-20 bg-background-dark">
    <div class="hero-gradient absolute inset-0 bg-background-dark"></div>
    <?php if (has_post_thumbnail()) : ?>
        <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'thanchi-hero')); ?>');"></div>
    <?php endif; ?>
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center">
        <h1 class="font-serif text-4xl md:text-6xl font-bold text-white"><?php the_title(); ?></h1>
    </div>
</section>

<!-- Page Content -->
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-4xl mx-auto px-6 lg:px-12">
        <div class="prose prose-lg dark:prose-invert max-w-none
            prose-headings:font-serif prose-headings:font-bold
            prose-h2:text-3xl prose-h2:mt-12 prose-h2:mb-6
            prose-h3:text-2xl prose-h3:mt-8 prose-h3:mb-4
            prose-p:text-[#7f756c] dark:prose-p:text-[#a9a29a] prose-p:leading-relaxed
            prose-a:text-primary prose-a:no-underline hover:prose-a:underline
            prose-strong:text-[#161413] dark:prose-strong:text-white
            prose-ul:text-[#7f756c] dark:prose-ul:text-[#a9a29a]
            prose-ol:text-[#7f756c] dark:prose-ol:text-[#a9a29a]
            prose-blockquote:border-l-primary prose-blockquote:bg-[#f2f1ef] dark:prose-blockquote:bg-[#25211c] prose-blockquote:py-4 prose-blockquote:px-6 prose-blockquote:rounded-r-lg
            prose-img:rounded-2xl prose-img:shadow-lg">
            <?php the_content(); ?>
        </div>
    </div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
