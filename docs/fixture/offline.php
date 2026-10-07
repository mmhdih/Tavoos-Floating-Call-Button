<?php
// Disposable documentation fixture only: do not contact outside services.
add_filter('pre_http_request', function(){ return new WP_Error('fixture_offline','External requests disabled in documentation fixture.'); });
// Only the fixture sets locale/direction without an unavailable core language pack.
add_filter('locale',function($locale){return get_option('tavoos_fixture_locale',$locale);});
add_action('after_setup_theme',function(){global $wp_locale;if(get_option('tavoos_fixture_locale')==='fa_IR'){$wp_locale->text_direction='rtl';}});
add_filter('show_admin_bar','__return_false');
