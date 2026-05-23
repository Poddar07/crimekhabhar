<?php
/**
 * Footer template.
 *
 * @package Bharat_Bulletin
 */
?>
	<footer class="site-footer">
		<div class="footer-inner">
			<section>
				<div class="footer-brand">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/crime-khabar-logo.jpeg' ); ?>" alt="<?php esc_attr_e( 'क्राइम खबर', 'bharat-bulletin' ); ?>">
					<div>
						<strong><?php bloginfo( 'name' ); ?></strong>
						<span><?php bloginfo( 'description' ); ?></span>
					</div>
				</div>
				<p>बिहार की अपराध, राजनीति, जिलों, शिक्षा, मौसम और नौकरी से जुड़ी भरोसेमंद खबरें।</p>
				<div class="social-links" aria-label="<?php esc_attr_e( 'Social links', 'bharat-bulletin' ); ?>">
					<a href="#">YouTube</a>
					<a href="#">Facebook</a>
					<a href="#">WhatsApp</a>
				</div>
			</section>
			<section class="footer-links">
				<h3><?php esc_html_e( 'सेक्शन', 'bharat-bulletin' ); ?></h3>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'fallback_cb'    => 'bharat_bulletin_footer_fallback',
					)
				);
				?>
			</section>
			<section class="footer-links">
				<h3><?php esc_html_e( 'रीडर सर्विस', 'bharat-bulletin' ); ?></h3>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/category/jobs/' ) ); ?>">नौकरी अपडेट</a></li>
					<li><a href="<?php echo esc_url( home_url( '/category/education/' ) ); ?>">शिक्षा</a></li>
					<li><a href="<?php echo esc_url( home_url( '/category/weather/' ) ); ?>">मौसम</a></li>
					<li><a href="<?php echo esc_url( home_url( '/category/video/' ) ); ?>">वीडियो</a></li>
				</ul>
			</section>
			<section>
				<h3><?php esc_html_e( 'संपर्क', 'bharat-bulletin' ); ?></h3>
				<ul class="footer-contact">
					<li>News Desk: Patna, Bihar</li>
					<li>Email: news@example.com</li>
					<li><a href="#">Advertise with us</a></li>
					<li><a href="#">Privacy Policy</a></li>
				</ul>
			</section>
		</div>
		<div class="copyright">© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.</div>
	</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
