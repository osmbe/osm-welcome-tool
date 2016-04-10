<?php
include_once('paths.php');
include_once(INCLUDES_PATH.'/page.php');

register_style('css/info.css');
page_start('Aide: accueillir les nouveaux contributeurs', 'info.php');

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
			<p>Cette section est écrite par un néerlandophone. Si vous avez le temps, corrigez-la s'il vous plaît!</p>
			<h3>Terminologie d'iD</h3>
			<p>iD utilise d'autres mots pour les primitives data. <span class='term-osm'>terminologie OSM normale</span> <span class='term-id'>terminologie iD</span>.</p>
			<ul>
				<li>un <span class='term-osm'>node</span> qui fait part d'un <span class='term-osm'>way</span> s'appelle un <span class='term-id'>nœud</span></li>
				<li>un <span class='term-osm'>node</span> singulier s'appelle un <span class='term-id'>point</span></li>
				<li>un <span class='term-osm'>way</span> qui signifie un région, s'appelle un <span class='term-id'>polygone</span></li>
				<li>un <span class='term-osm'>way</span> qui ne signifie pas de région, s'appelle une <span class='term-id'>ligne</span></li>
				<li>les <span class='term-osm'>multipolygones</span> sont créés automatiquement et sont également indiqués comme <span class='term-id'>polygone</span> au contributeur, il ou elle ne sait pas qu'on a fait une <span class='term-osm'>relation</span></li>
			</ul>

			<p>Si vous envoyez des messages aux utilisateurs d'iD, vous pouvez rendre compte de cela pour que vous ne les mêliez pas.</p>
		</section>
	</article>

<?php
page_end();
?>