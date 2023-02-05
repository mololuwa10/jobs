<?php

namespace Database;
use controller\PageController;
use controller\AdminController;
use JetBrains\PhpStorm\NoReturn;

class AdminValidation
{
    public function adminValidation()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
            header("Location: adminLogin");
            exit();
        }
        if (isset($_SESSION['userDetails']['userType']) && $_SESSION['userDetails']['userType'] != 'admin') {
            header("Location: clientIndex");
            exit();
        }
    }

}