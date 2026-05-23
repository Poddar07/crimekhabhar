<?php
/**
 * Main homepage template.
 *
 * @package Bharat_Bulletin
 */

get_header();

$sample_posts = array(
	array( 'title' => 'पटना में अपराधियों पर नकेल कसने के लिए विशेष अभियान, कई इलाकों में चेकिंग तेज', 'badge' => 'बड़ी खबर', 'excerpt' => 'राजधानी में बढ़ती वारदातों के बाद पुलिस ने रात की गश्ती और संवेदनशील इलाकों की निगरानी बढ़ाई।' ),
	array( 'title' => 'सीमांचल और मिथिलांचल में तेज हवा के साथ बारिश की संभावना', 'badge' => 'मौसम', 'excerpt' => 'मौसम विभाग ने किसानों और यात्रियों के लिए ताजा सलाह जारी की।' ),
	array( 'title' => 'विधानसभा चुनाव से पहले बिहार में सियासी समीकरणों पर चर्चा तेज', 'badge' => 'राजनीति', 'excerpt' => 'ग्रामीण इलाकों में नेताओं के दौरे और बैठकों का दौर शुरू हो गया है।' ),
	array( 'title' => 'युवा अभ्यर्थियों के लिए भर्ती कैलेंडर और परीक्षा तारीखों पर बड़ी अपडेट', 'badge' => 'नौकरी', 'excerpt' => 'आवेदन, योग्यता और परीक्षा पैटर्न पर नए निर्देश जल्द जारी हो सकते हैं।' ),
	array( 'title' => 'मुजफ्फरपुर में सड़क जाम से लोग परेशान, प्रशासन ने रूट डायवर्जन किया', 'badge' => 'जिले', 'excerpt' => 'स्कूल और बाजार समय में ट्रैफिक दबाव कम करने की नई योजना लागू।' ),
	array( 'title' => 'BSEB परीक्षा फॉर्म और एडमिट कार्ड को लेकर छात्रों के लिए जरूरी सूचना', 'badge' => 'शिक्षा', 'excerpt' => 'बोर्ड ने स्कूलों को समय पर डेटा अपडेट करने का निर्देश दिया।' ),
);
?>

<main class="content-shell">
	<div class="main-content">
		<section class="lead-grid">
			<?php
			if ( have_posts() ) :
				the_post();
				bharat_bulletin_card(
					array(
						'title'    => get_the_title(),
						'excerpt'  => get_the_excerpt(),
						'url'      => get_permalink(),
						'image'    => has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'bb-featured' ) : '',
						'badge'    => 'Top Story',
						'featured' => true,
						'index'    => 1,
					)
				);
			else :
				bharat_bulletin_card(
					array(
						'title'    => $sample_posts[0]['title'],
						'excerpt'  => $sample_posts[0]['excerpt'],
						'badge'    => $sample_posts[0]['badge'],
						'featured' => true,
						'index'    => 1,
					)
				);
			endif;
			?>
			<div class="stacked-news">
				<?php
				for ( $i = 1; $i <= 3; $i++ ) :
					$item = $sample_posts[ $i ];
					?>
					<article class="story-mini">
						<a class="thumb" href="#"><img src="<?php echo esc_url( bharat_bulletin_sample_image( $i + 1 ) ); ?>" alt=""></a>
						<div>
							<h3><a href="#"><?php echo esc_html( $item['title'] ); ?></a></h3>
							<div class="meta"><span><?php echo esc_html( $item['badge'] ); ?></span><span>•</span><span>ताजा</span></div>
						</div>
					</article>
				<?php endfor; ?>
			</div>
		</section>

		<section class="section">
			<div class="section-head">
				<h2 class="section-title">बिहार की बड़ी ख़बरें</h2>
				<a class="section-link" href="#">और भी</a>
			</div>
			<div class="news-grid">
				<?php
				for ( $i = 0; $i < 6; $i++ ) :
					bharat_bulletin_card(
						array(
							'title'   => $sample_posts[ $i % count( $sample_posts ) ]['title'],
							'excerpt' => $sample_posts[ $i % count( $sample_posts ) ]['excerpt'],
							'badge'   => $sample_posts[ $i % count( $sample_posts ) ]['badge'],
							'index'   => $i + 2,
						)
					);
				endfor;
				?>
			</div>
		</section>

		<section class="section video-band">
			<div class="section-head">
				<h2 class="section-title">बिहार वीडियो</h2>
				<a class="section-link" href="#">सभी वीडियो</a>
			</div>
			<div class="video-row">
				<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
					<article class="video-card">
						<a class="story-media" href="#">
							<img src="<?php echo esc_url( bharat_bulletin_sample_image( $i + 8 ) ); ?>" alt="">
							<span class="media-badge">▶</span>
							<span class="duration">0<?php echo esc_html( $i ); ?>:<?php echo esc_html( 20 + $i ); ?></span>
						</a>
						<h3><a href="#">ग्राउंड रिपोर्ट: बिहार के जिलों से दिनभर की बड़ी अपडेट</a></h3>
					</article>
				<?php endfor; ?>
			</div>
		</section>

		<section class="section">
			<div class="section-head">
				<h2 class="section-title">बिहार विजुअल स्टोरीज़</h2>
				<a class="section-link" href="#">देखें</a>
			</div>
			<div class="visual-strip">
				<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
					<a class="visual-card" href="#">
						<img src="<?php echo esc_url( bharat_bulletin_sample_image( $i + 14 ) ); ?>" alt="">
						<h3>आज की तस्वीरों में बिहार की खास झलक</h3>
					</a>
				<?php endfor; ?>
			</div>
		</section>
	</div>

	<?php get_sidebar(); ?>
</main>

<?php get_footer(); ?>
