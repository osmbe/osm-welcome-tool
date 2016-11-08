<?php

$language = 'fr';
$welcome[$language] = array();


$welcome[$language]['language_name'] = 'Français';

$welcome[$language]['hi'] = 'Salut %1$s';

$welcome[$language]['bravo'] = 'Nous avons remarqué que vous avez effectué votre première contribution sur OpenStreetMap. Bravo et merci! De ce fait vous devenez aussi membre de la communauté OSM.';

$welcome[$language]['reality'] = 'Nous n’existons pas uniquement sur le plan '.markdown_link('virtuel', 'http://osm.be/').' mais également '.markdown_link('dans la réalité', 'https://www.meetup.com/OpenStreetMap-Belgium/').'.';

$welcome[$language]['questions'] = 'Si vous avez des questions, nous sommes en mesure de vous venir en aide : si vous pensez que quelque chose est anormal, si vous avez un doute sur la manière de cartographier ou si vous voulez en savoir plus sur OpenStreetMap, alors prenez contact avec nous.';

$welcome[$language]['helpintro'] = 'Nous vous aiderons volontiers de diverses manières :';

$welcome[$language]['info_wiki'] = 'Vous pouvez trouver beaucoup d’informations via '.markdown_link('le wiki', 'https://wiki.openstreetmap.org/wiki/FR:Page_principale').'.';

// Customized info for the mapper's editor
$welcome[$language]['info_iD'] = 'Si vous ne trouvez pas comment faire dans iD, la manière la plus rapide de trouver plus facile est de'; // Followed by info_solution
$welcome[$language]['info_Potlatch'] = 'Si vous ne trouvez pas comment faire dans Potlatch, la manière la plus rapide de trouver plus facile est de'; // Followed by info_solution
$welcome[$language]['info_JOSM'] = '<span style="font-size:200%;color:#f00;">*TRADUCTION MANQUANTE - VEUILLEZ TRADUIRE ET CONTACTER <a href="https://www.openstreetmap.org/message/new/M!dgard">M!DGARD</a>*</span> Als je dingen wilt mappen met JOSM, kunnen de presets je helpen. Het is een goed idee eens in de wiki te kijken om te zien hoe een tag precies gebruikt wordt. Er staan hyperlinks bij de presets. De snelste manier om iets anders in de wiki te vinden is door'; // Followed by info_solution
$welcome[$language]['info_other'] = '<span style="font-size:200%;color:#f00;">*TRADUCTION MANQUANTE - VEUILLEZ TRADUIRE ET CONTACTER <a href="https://www.openstreetmap.org/message/new/M!dgard">M!DGARD</a>*</span> Als je wil weten hoe je iets moet taggen, is de snelste oplossing'; // Followed by info_solution

$welcome[$language]['info_solution'] = 'faire une recherche en ligne avec “site:wiki.openstreetmap.org [terme recherché]”. Vous pouvez également aller sur '.markdown_link('le site d’aide', 'https://help.openstreetmap.org').' ou poser une question sur '.markdown_link('le forum', 'https://forum.openstreetmap.org/viewforum.php?id=29').'.';

$welcome[$language]['news'] = 'Si vous souhaitez recevoir les nouvelles de la communauté OSM belge, enregistrez-vous alors sur la '.markdown_link('mailing-list belge', 'https://lists.openstreetmap.org/listinfo/talk-be').' ou abonnez-vous à la '.markdown_link('lettre d\'information belge', 'http://osm.us13.list-manage.com/subscribe?u=cc6632a49e784f67574e50269&id=5c2416bba6').'. Vous pouvez aussi suiver '.markdown_link('@osm_be', 'https://twitter.com/osm_be').' sur Twitter, devener membre du '.markdown_link('groupe Meetup', 'https://www.meetup.com/OpenStreetMap-Belgium/').', du '.markdown_link('groupe Facebook', 'https://www.facebook.com/groups/1419016881706058/').' ou de la '.markdown_link('communauté Google+', 'https://plus.google.com/communities/114637875336935397552').'.';

$welcome[$language]['resultmaps'] = 'Souhaitez-vous savoir qui est actif dans votre région ou quantifier vos contributions, consultez alors les cartes sur '.markdown_link('resultmaps.neis-one.org', 'http://resultmaps.neis-one.org/').'.';

$welcome[$language]['weeklyosm'] = 'Il existe également une '.markdown_link('revue hebdomadaire internationale du monde OpenStreetMap', 'http://www.weeklyosm.eu/').' à laquelle vous pouvez vous abonner. (Il y a aussi un flux RSS pour ceux qui aiment ça.)';

$welcome[$language]['endingsentence'] = 'Happy Mapping!';

$welcome[$language]['osm-be'] = 'OpenStreetMap Belgique';

$welcome[$language]['multiple_langs'] = 'Ce message est envoyé en plusieurs langues parce que nous ne pouvions pas détecter votre langue.';
