TextPressSearch
===============

TextPress Search Page

 - Add this search.php to your textpress install , in 2.0 it would be /themes/textpress/search.php

 - Add route to /config/config.php:
 - 
			'search' => array(
					'route' => '/search',
					'template' => 'search'
				),
