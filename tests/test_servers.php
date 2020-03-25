<?php

namespace Test;

use WecarSwoole\SubServer\Servers;

include_once './base.php';

$servers = Servers::getInstance();

var_export($servers->aliasMap);