<?php
include_once('paths.php');
include_once(INCLUDES_PATH.'/page.php');

register_style('css/info.css');
page_start('Hulp bij nieuwe gebruikers verwelkomen', 'info.php');

function markdown_link ($text, $url) {
	echo '<span class="invisible">[</span><a href="';
	echo $url;
	echo '">';
	echo $text;
	echo '</a><span class="invisible">](';
	echo $url;
	echo ')</span>';
}
?>

	<article>
		<section id="terms">
			<h3>Terminologie van iD</h3>
			<p>iD heeft een consequente maar afwijkende terminologie op vlak van dataprimitieven. <span class='term-osm'>gewone OSM-term</span> <span class='term-id'>term in iD</span></p>
			<ul>
				<li>een <span class='term-osm'>node</span> als deel van een <span class='term-osm'>way</span> heet een <span class='term-id'>knooppunt</span></li>
				<li>een losse <span class='term-osm'>node</span> heet een <span class='term-id'>punt</span></li>
				<li>een <span class='term-osm'>way</span> die een gebied aanduidt, heet een <span class='term-id'>vlak</span></li>
				<li>een <span class='term-osm'>way</span> die geen gebied aanduidt, heet een <span class='term-id'>lijn</span></li>
				<li><span class='term-osm'>multipolygonen</span> worden automatisch aangemaakt en worden ook als <span class='term-id'>vlak</span> weergegeven aan de gebruiker, hij/zij heeft er geen besef van dat er een <span class='term-osm'>relatie</span> is aangemaakt</li>
			</ul>

			<p>Als je berichten stuurt naar nieuwe mappers die iD gebruiken, kun je hier rekening mee houden om hen niet te verwarren.</p>
		</section>
	</article>

<?php
page_end();
?>