<?php
$app = require_once dirname(__FILE__) . '/../bootstrap.php';

include dirname(__FILE__) . '/../src/controllers/server_ips.php';
//first
$app->run();