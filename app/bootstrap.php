<?php

require_once __DIR__.'/../conf/define.php';
require_once DIR_HELPER.'/ClassLoader.php';
$class_loader = new ClassLoader(null, DIR_BASE);
$class_loader->register();

require_once DIR_VENDOR.'/autoload.php';
