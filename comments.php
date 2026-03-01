<?php
/**
 * Comments Template
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if (have_comments()) : ?>

        <h2 id="comments-title" class="font-serif text-2xl font-bold mb-8">
            <?php
            $comment_count = get_comments_number();
            if ('1' === $comment_count) {
                printf(
                    esc_html__('One comment on "%1$s"', 'thanchi-eco-resort'),
                    '<span>' . wp_kses_post(get_the_title()) . '</span>'
                );
            } else {
                printf(
                    esc_html(_nx('%1$s comment on "%2$s"', '%1$s comments on "%2$s"', $comment_count, 'comments title', 'thanchi-eco-resort')),
                    number_format_i18n($comment_count),
                    '<span>' . wp_kses_post(get_the_title()) . '</span>'
                );
            }
            ?>
        </h2>

        <?php the_comments_navigation(); ?>

        <ol class="space-y-6" style="list-style: none; padding: 0;">
            <?php
            wp_list_comments(array(
                'style' => 'ol',
                'short_ping' => true,
                'avatar_size' => 48,
            ));
            ?>
        </ol>

        <?php the_comments_navigation(); ?>

        <?php if (!comments_open()) : ?>
            <p class="text-center p-6 bg-[#f2f1ef] dark:bg-[#25211c] rounded-xl text-[#7f756c] dark:text-[#a9a29a] mt-8">
                <?php esc_html_e('Comments are closed.', 'thanchi-eco-resort'); ?>
            </p>
        <?php endif; ?>

    <?php endif; ?>

    <?php
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');

    comment_form(array(
        'title_reply' => esc_html__('Leave a Comment', 'thanchi-eco-resort'),
        'title_reply_to' => esc_html__('Leave a Reply to %s', 'thanchi-eco-resort'),
        'cancel_reply_link' => esc_html__('Cancel Reply', 'thanchi-eco-resort'),
        'label_submit' => esc_html__('Post Comment', 'thanchi-eco-resort'),
        'comment_field' => '<div class="mb-6"><label for="comment" class="block text-sm font-bold mb-2">' . esc_html__('Comment', 'thanchi-eco-resort') . ' <span class="text-red-500">*</span></label><textarea id="comment" name="comment" rows="5" required aria-required="true" class="w-full px-4 py-3 border border-[#e3e0de] dark:border-[#3a342e] rounded-lg bg-background-light dark:bg-background-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all resize-y"></textarea></div>',
        'fields' => array(
            'author' => '<div class="mb-6"><label for="author" class="block text-sm font-bold mb-2">' . esc_html__('Name', 'thanchi-eco-resort') . ($req ? ' <span class="text-red-500">*</span>' : '') . '</label><input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '"' . $aria_req . ' class="w-full px-4 py-3 border border-[#e3e0de] dark:border-[#3a342e] rounded-lg bg-background-light dark:bg-background-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all" /></div>',
            'email' => '<div class="mb-6"><label for="email" class="block text-sm font-bold mb-2">' . esc_html__('Email', 'thanchi-eco-resort') . ($req ? ' <span class="text-red-500">*</span>' : '') . '</label><input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '"' . $aria_req . ' class="w-full px-4 py-3 border border-[#e3e0de] dark:border-[#3a342e] rounded-lg bg-background-light dark:bg-background-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all" /></div>',
            'url' => '<div class="mb-6"><label for="url" class="block text-sm font-bold mb-2">' . esc_html__('Website', 'thanchi-eco-resort') . '</label><input id="url" name="url" type="url" value="' . esc_attr($commenter['comment_author_url']) . '" class="w-full px-4 py-3 border border-[#e3e0de] dark:border-[#3a342e] rounded-lg bg-background-light dark:bg-background-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all" /></div>',
        ),
        'class_form' => 'mt-12',
        'class_submit' => 'bg-primary hover:bg-[#855935] text-white px-8 py-3 rounded-lg font-bold transition-all cursor-pointer',
        'submit_button' => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
    ));
    ?>

</div>
