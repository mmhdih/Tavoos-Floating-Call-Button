<?php
require __DIR__.'/wp-load.php';
if (!current_user_can('manage_options')) { http_response_code(403); exit; }
switch_theme('tavoos-demo');
$s=Tavoos_FCB_Options::get();
if (isset($_GET['lang'])) {
 $fa=$_GET['lang']==='fa'; update_option('tavoos_fixture_locale',$fa?'fa_IR':'en_US'); update_user_meta(get_current_user_id(),'locale',$fa?'fa_IR':'en_US'); update_option('WPLANG',$fa?'fa_IR':'');
 $s['direction']=$fa?'rtl':'auto';
 $s['aria_label']=$fa?'راه‌های تماس با ما':'Contact us';
 $titles=$fa?array('تماس با استودیو','ارسال ایمیل','مرکز راهنما'):array('Call our studio','Send an email','Help center');
 $subs=$fa?array('شماره آزمایشی','درباره پروژه‌تان بنویسید','پاسخ پرسش‌های شما'):array('Demo number · no calls sent','Tell us about your project','Find answers at your pace');
 foreach($s['channels'] as $i=>&$c){$c['title']=$titles[$i];$c['subtitle']=$subs[$i];} unset($c);
}
if(isset($_GET['position'])){$s['position']=$_GET['position']==='custom'?'custom':'bottom-right';$s['custom_x']=15;$s['custom_y']=80;}
update_option(TAVOOS_FCB_OPTION,$s);
echo 'Fixture ready; WordPress '.get_bloginfo('version').' PHP '.PHP_VERSION;
