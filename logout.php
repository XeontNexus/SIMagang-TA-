<?php
require_once 'config/config.php';

session_destroy();
redirect(base_url('login.php'));
