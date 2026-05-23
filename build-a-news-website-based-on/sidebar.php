<?php
/**
 * Sidebar template.
 *
 * @package Bharat_Bulletin
 */
?>
<aside class="sidebar">
	<div class="ad-box">Advertisement</div>

	<section class="side-widget">
		<h2>Trending Bihar News</h2>
		<ol class="ranked-list">
			<li><span class="rank">1</span><a href="#">पटना में चेकिंग अभियान, कई थाना क्षेत्रों में कार्रवाई</a></li>
			<li><span class="rank">2</span><a href="#">मौसम विभाग ने उत्तर बिहार के लिए अलर्ट जारी किया</a></li>
			<li><span class="rank">3</span><a href="#">सरकारी नौकरी की तैयारी कर रहे युवाओं के लिए बड़ी खबर</a></li>
			<li><span class="rank">4</span><a href="#">BSEB छात्रों के लिए फॉर्म भरने की अंतिम तारीख नजदीक</a></li>
			<li><span class="rank">5</span><a href="#">भागलपुर, गया और दरभंगा से दिनभर की बड़ी अपडेट</a></li>
		</ol>
	</section>

	<section class="side-widget weather-widget">
		<h2>बिहार मौसम</h2>
		<div class="weather-temp">34°C</div>
		<p>पटना · उमस के साथ बादल छाए रहेंगे</p>
	</section>

	<?php if ( is_active_sidebar( 'homepage-sidebar' ) ) : ?>
		<?php dynamic_sidebar( 'homepage-sidebar' ); ?>
	<?php endif; ?>

	<section class="side-widget newsletter">
		<h2>बिहार न्यूज़ अलर्ट</h2>
		<p>सुबह की सबसे जरूरी बिहार खबरें अपने ईमेल पर पाएं।</p>
		<form>
			<input type="email" placeholder="Email address">
			<button type="submit">Subscribe</button>
		</form>
	</section>

	<section class="side-widget">
		<h2>फोटो</h2>
		<ul class="plain-list">
			<li><a href="#">पटना की सुबह और गंगा घाट की तस्वीरें</a></li>
			<li><a href="#">उत्तर बिहार के गांवों से मौसम की झलक</a></li>
			<li><a href="#">बिहार की राजनीति के पांच बड़े चेहरे</a></li>
		</ul>
	</section>
</aside>
