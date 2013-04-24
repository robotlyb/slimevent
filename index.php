<?php

date_default_timezone_set('Asia/Harbin');

require __DIR__.'/app/lib/base.php';

F3::config('app/cfg/setup.cfg');
F3::config('app/cfg/constant.cfg');
F3::config('app/cfg/index.cfg');
F3::config('app/cfg/errno.cfg');
F3::config('app/cfg/routes.cfg');

#已开通的社团账号，但是还没有被社团认领
F3::set('alone_clubs',array('201','新基论坛','星期舞'));

F3::set('DB', new DB(
	'mysql:host=localhost;port=3306;dbname=slimevent',
	//'mysql:host=192.168.17.254;port=3306;dbname=slimevent',
	'root',
	'vpcm'
));

F3::run();

?>
