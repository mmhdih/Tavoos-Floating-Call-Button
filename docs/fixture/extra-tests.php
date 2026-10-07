<?php
require __DIR__.'/wp-load.php';
if (!current_user_can('manage_options')) {http_response_code(403);exit;}
$checks=0;
function check_fixture($ok,$message){global $checks;if(!$ok)throw new Exception($message);$checks++;echo "PASS: $message\n";}
$cases=array('phone'=>array('+۱۲۰۲۵۵۵۰۱۲۳','tel:+12025550123'),'whatsapp'=>array('0012025550123','https://wa.me/12025550123'),'telegram'=>array('@example','https://t.me/example'),'instagram'=>array('example','https://instagram.com/example'),'email'=>array('hello@example.com','mailto:hello@example.com'),'sms'=>array('+12025550123','sms:+12025550123'),'eitaa'=>array('example','https://eitaa.com/example'),'bale'=>array('example','https://ble.ir/example'),'rubika'=>array('example','https://rubika.ir/example'),'linkedin'=>array('example','https://www.linkedin.com/in/example'),'location'=>array('https://example.com/map','https://example.com/map'),'custom'=>array('example.com/support','https://example.com/support'));
foreach($cases as $type=>$pair){check_fixture(Tavoos_FCB_Frontend::build_url(Tavoos_FCB_Options::channel_defaults($type,array('value'=>$pair[0])))===$pair[1],"$type URL matches guide");}
$c=Tavoos_FCB_Options::channel_defaults('whatsapp',array('value'=>'https://example.com/full','message'=>'Hello'));
check_fixture(Tavoos_FCB_Frontend::build_url($c)==='https://example.com/full','full URL bypasses message append');
check_fixture(count(tavoos_fcb_get_icons())-1===18,'18 visible presets excluding close');
foreach(glob(TAVOOS_FCB_DIR.'*.php') as $file){token_get_all(file_get_contents($file),TOKEN_PARSE);check_fixture(true,basename($file).' syntax parsed');}
foreach(glob(TAVOOS_FCB_DIR.'includes/*.php') as $file){token_get_all(file_get_contents($file),TOKEN_PARSE);check_fixture(true,basename($file).' syntax parsed');}
echo "Completed $checks additional source and PHP checks\n";
