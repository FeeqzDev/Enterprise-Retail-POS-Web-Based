<?php
// Configuration Template
// RENAME TO: config.php

define('DB_HOST', 'localhost');
define('DB_NAME', 'erp_production');
define('DB_USER', 'root');
define('DB_PASS', 'your_password_here');

// App Settings
define('APP_ENV', 'production'); // 'development' or 'production'
define('APP_DEBUG', false);
define('TIMEZONE', 'Asia/Kuala_Lumpur');

// Security Keys
define('CSRF_SECRET', 'change_this_to_random_string');
?>
