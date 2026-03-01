<?php
/**
 * Template Name: Contact Page
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<!-- Page Header -->
<section class="relative py-32 pt-40 -mt-20 bg-background-dark">
    <div class="hero-gradient absolute inset-0 bg-background-dark"></div>
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center">
        <span class="text-primary font-bold tracking-widest text-sm uppercase mb-4 block"><?php esc_html_e('Get In Touch', 'thanchi-eco-resort'); ?></span>
        <h1 class="font-serif text-4xl md:text-6xl font-bold text-white mb-6"><?php esc_html_e('Contact Us', 'thanchi-eco-resort'); ?></h1>
        <p class="text-lg text-[#a9a29a] max-w-2xl mx-auto">
            <?php esc_html_e('Have questions? Want to book? Need directions? We are here to help.', 'thanchi-eco-resort'); ?>
        </p>
    </div>
</section>

<!-- Contact Content -->
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16">

            <!-- Contact Information -->
            <div>
                <h2 class="font-serif text-3xl font-bold mb-8"><?php esc_html_e('Get in Touch', 'thanchi-eco-resort'); ?></h2>

                <div class="space-y-8">
                    <div class="flex gap-4">
                        <div class="size-12 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-primary">call</span>
                        </div>
                        <div>
                            <p class="font-bold mb-1"><?php esc_html_e('Phone', 'thanchi-eco-resort'); ?></p>
                            <p class="text-[#7f756c] dark:text-[#a9a29a]">
                                <a href="tel:+8801234567890" class="hover:text-primary transition-colors">+880 1234 567 890</a>
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="size-12 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-primary">chat</span>
                        </div>
                        <div>
                            <p class="font-bold mb-1"><?php esc_html_e('WhatsApp (Preferred)', 'thanchi-eco-resort'); ?></p>
                            <p class="text-[#7f756c] dark:text-[#a9a29a]">
                                <a href="https://wa.me/8801234567890" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors">+880 1234 567 890</a>
                            </p>
                            <p class="text-xs text-[#7f756c] mt-1">
                                <?php esc_html_e('Note: Network in Thanchi is limited. We check WhatsApp when we go to town.', 'thanchi-eco-resort'); ?>
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="size-12 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-primary">mail</span>
                        </div>
                        <div>
                            <p class="font-bold mb-1"><?php esc_html_e('Email', 'thanchi-eco-resort'); ?></p>
                            <p class="text-[#7f756c] dark:text-[#a9a29a]">
                                <a href="mailto:hello@thanchiecoresort.com" class="hover:text-primary transition-colors">hello@thanchiecoresort.com</a>
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="size-12 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-primary">location_on</span>
                        </div>
                        <div>
                            <p class="font-bold mb-1"><?php esc_html_e('Address', 'thanchi-eco-resort'); ?></p>
                            <p class="text-[#7f756c] dark:text-[#a9a29a]">
                                <?php esc_html_e('Thanchi Eco Resort', 'thanchi-eco-resort'); ?><br>
                                <?php esc_html_e('Thanchi Upazila', 'thanchi-eco-resort'); ?><br>
                                <?php esc_html_e('Bandarban Hill District', 'thanchi-eco-resort'); ?><br>
                                <?php esc_html_e('Chittagong Division, Bangladesh', 'thanchi-eco-resort'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- How to Reach -->
                <div class="mt-12 pt-12 border-t border-[#e3e0de] dark:border-[#3a342e]">
                    <h3 class="font-serif text-2xl font-bold mb-6"><?php esc_html_e('How to Reach Thanchi', 'thanchi-eco-resort'); ?></h3>

                    <div class="space-y-6 text-sm text-[#7f756c] dark:text-[#a9a29a]">
                        <div>
                            <p class="font-bold text-[#161413] dark:text-white mb-1"><?php esc_html_e('From Dhaka:', 'thanchi-eco-resort'); ?></p>
                            <p><?php esc_html_e('Take a bus to Bandarban town (8-10 hours overnight). From Bandarban, take a local bus to Thanchi (3-4 hours, stunning views). We can pick you up from Thanchi bus stand.', 'thanchi-eco-resort'); ?></p>
                        </div>

                        <div>
                            <p class="font-bold text-[#161413] dark:text-white mb-1"><?php esc_html_e('From Chittagong:', 'thanchi-eco-resort'); ?></p>
                            <p><?php esc_html_e('Take a bus to Bandarban (2-3 hours), then follow the same route to Thanchi.', 'thanchi-eco-resort'); ?></p>
                        </div>

                        <div>
                            <p class="font-bold text-[#161413] dark:text-white mb-1"><?php esc_html_e('Boat Option:', 'thanchi-eco-resort'); ?></p>
                            <p><?php esc_html_e('During monsoon, you can take a boat along the Sangu River. The most scenic way to arrive.', 'thanchi-eco-resort'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white dark:bg-[#25211c] p-8 lg:p-12 rounded-2xl shadow-md">
                <h2 class="font-serif text-2xl font-bold mb-8"><?php esc_html_e('Send Us a Message', 'thanchi-eco-resort'); ?></h2>

                <?php
                // Always show the built-in form for this theme
                // To use Contact Form 7 or WPForms, replace this section with the appropriate shortcode
                ?>
                    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="contact-form" class="space-y-6">
                        <?php wp_nonce_field('thanchi_contact_form', 'thanchi_contact_nonce'); ?>
                        <input type="hidden" name="action" value="thanchi_contact_form">

                        <div>
                            <label for="contact-name" class="block text-sm font-bold mb-2"><?php esc_html_e('Your Name', 'thanchi-eco-resort'); ?> <span class="text-red-500">*</span></label>
                            <input type="text" id="contact-name" name="name" required aria-required="true"
                                class="w-full px-4 py-3 border border-[#e3e0de] dark:border-[#3a342e] rounded-lg bg-background-light dark:bg-background-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        </div>

                        <div>
                            <label for="contact-email" class="block text-sm font-bold mb-2"><?php esc_html_e('Email Address', 'thanchi-eco-resort'); ?> <span class="text-red-500">*</span></label>
                            <input type="email" id="contact-email" name="email" required aria-required="true"
                                class="w-full px-4 py-3 border border-[#e3e0de] dark:border-[#3a342e] rounded-lg bg-background-light dark:bg-background-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        </div>

                        <div>
                            <label for="contact-phone" class="block text-sm font-bold mb-2"><?php esc_html_e('Phone / WhatsApp', 'thanchi-eco-resort'); ?></label>
                            <input type="tel" id="contact-phone" name="phone"
                                class="w-full px-4 py-3 border border-[#e3e0de] dark:border-[#3a342e] rounded-lg bg-background-light dark:bg-background-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        </div>

                        <div>
                            <label for="contact-subject" class="block text-sm font-bold mb-2"><?php esc_html_e('Subject', 'thanchi-eco-resort'); ?></label>
                            <select id="contact-subject" name="subject"
                                class="w-full px-4 py-3 border border-[#e3e0de] dark:border-[#3a342e] rounded-lg bg-background-light dark:bg-background-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                                <option value="booking"><?php esc_html_e('Room Booking Inquiry', 'thanchi-eco-resort'); ?></option>
                                <option value="general"><?php esc_html_e('General Inquiry', 'thanchi-eco-resort'); ?></option>
                                <option value="directions"><?php esc_html_e('Directions & Transportation', 'thanchi-eco-resort'); ?></option>
                                <option value="group"><?php esc_html_e('Group Booking', 'thanchi-eco-resort'); ?></option>
                                <option value="other"><?php esc_html_e('Other', 'thanchi-eco-resort'); ?></option>
                            </select>
                        </div>

                        <div>
                            <label for="contact-dates" class="block text-sm font-bold mb-2"><?php esc_html_e('Preferred Dates (if booking)', 'thanchi-eco-resort'); ?></label>
                            <input type="text" id="contact-dates" name="dates" placeholder="<?php esc_attr_e('e.g., Dec 15-18, 2025', 'thanchi-eco-resort'); ?>"
                                class="w-full px-4 py-3 border border-[#e3e0de] dark:border-[#3a342e] rounded-lg bg-background-light dark:bg-background-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        </div>

                        <div>
                            <label for="contact-message" class="block text-sm font-bold mb-2"><?php esc_html_e('Your Message', 'thanchi-eco-resort'); ?> <span class="text-red-500">*</span></label>
                            <textarea id="contact-message" name="message" rows="5" required aria-required="true"
                                class="w-full px-4 py-3 border border-[#e3e0de] dark:border-[#3a342e] rounded-lg bg-background-light dark:bg-background-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all resize-y"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-primary hover:bg-[#855935] text-white py-4 rounded-lg font-bold transition-all">
                            <?php esc_html_e('Send Message', 'thanchi-eco-resort'); ?>
                        </button>

                        <p class="text-xs text-[#7f756c] dark:text-[#a9a29a] text-center">
                            <?php esc_html_e('We usually respond within 24-48 hours. For urgent bookings, please use WhatsApp.', 'thanchi-eco-resort'); ?>
                        </p>
                    </form>
            </div>

        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-24 bg-[#f2f1ef] dark:bg-[#25211c]">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="text-center mb-12">
            <h2 class="font-serif text-4xl font-bold mb-4"><?php esc_html_e('Find Us on the Map', 'thanchi-eco-resort'); ?></h2>
        </div>

        <div class="rounded-2xl overflow-hidden shadow-lg" style="aspect-ratio: 21/9;">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d117551.58749947873!2d92.41!3d21.75!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30adc8a8a8a8a8a8%3A0x0!2sThanchi%2C%20Bangladesh!5e0!3m2!1sen!2sbd!4v1600000000000!5m2!1sen!2sbd"
                width="100%"
                height="100%"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="<?php esc_attr_e('Map showing location of Thanchi Eco Resort', 'thanchi-eco-resort'); ?>"
            ></iframe>
        </div>

        <p class="text-center mt-6 text-sm text-[#7f756c] dark:text-[#a9a29a]">
            <?php esc_html_e('GPS Coordinates: 21.7547, 92.4847 (approximate - exact location shared after booking)', 'thanchi-eco-resort'); ?>
        </p>
    </div>
</section>

<?php get_footer(); ?>
