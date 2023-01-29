<?php

namespace Database;

class AdminValidation
{

    public function adminValidation(): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if ($_SESSION['userDetails']['userType'] != 'admin') {
            header("Location: clientIndex");
            exit();
        }
    }

}