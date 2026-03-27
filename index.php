<?php
require_once 'config/config.php';

if(isLoggedIn()) {
    redirect(base_url('dashboard.php'));
} else {
    redirect(base_url('login.php'));
}
