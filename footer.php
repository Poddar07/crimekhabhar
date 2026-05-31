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
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/crime-khabar-logo.jpeg' ); ?>" alt="<?php esc_attr_e( 'Crime Khabar', 'bharat-bulletin' ); ?>">
					<div>
						<strong><?php bloginfo( 'name' ); ?></strong>
						<span><?php bloginfo( 'description' ); ?></span>
					</div>
				</div>
				<p><?php esc_html_e( 'Trusted Bihar updates on crime, districts, politics, education, weather, jobs and public-interest stories.', 'bharat-bulletin' ); ?></p>
				<div class="social-links" aria-label="<?php esc_attr_e( 'Social links', 'bharat-bulletin' ); ?>">
					<a href="#">YouTube</a>
					<a href="#">Facebook</a>
					<a href="#">WhatsApp</a>
				</div>
			</section>
			<section class="footer-links">
				<h3><?php esc_html_e( 'Sections', 'bharat-bulletin' ); ?></h3>
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
				<h3><?php esc_html_e( 'Reader Service', 'bharat-bulletin' ); ?></h3>
				<ul>
					<li><a href="<?php echo esc_url( bharat_bulletin_category_link( 'sarkari_naukri' ) ); ?>"><?php esc_html_e( 'Sarkari Naukri', 'bharat-bulletin' ); ?></a></li>
					<li><a href="<?php echo esc_url( bharat_bulletin_category_link( 'bseb' ) ); ?>">BSEB</a></li>
					<li><a href="<?php echo esc_url( bharat_bulletin_category_link( 'bihar_weather' ) ); ?>"><?php esc_html_e( 'Bihar Weather', 'bharat-bulletin' ); ?></a></li>
					<li><a href="<?php echo esc_url( bharat_bulletin_category_link( 'bihar_visual_stories' ) ); ?>"><?php esc_html_e( 'Visual Stories', 'bharat-bulletin' ); ?></a></li>
				</ul>
			</section>
			<section class="footer-links">
				<h3><?php esc_html_e( 'Legal', 'bharat-bulletin' ); ?></h3>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/privacy-policy.html' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'bharat-bulletin' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/terms-and-conditions.html' ) ); ?>"><?php esc_html_e( 'Terms and Conditions', 'bharat-bulletin' ); ?></a></li>
					<li><a href="#"><?php esc_html_e( 'Advertise with us', 'bharat-bulletin' ); ?></a></li>
				</ul>
			</section>
			<section>
				<h3><?php esc_html_e( 'Contact', 'bharat-bulletin' ); ?></h3>
				<ul class="footer-contact">
					<li>News Desk: Patna, Bihar</li>
					<li>Email: news@example.com</li>
					<li>Crime Khabar Digital Desk</li>
				</ul>
			</section>
		</div>
		<div class="copyright">© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.</div>
	</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
