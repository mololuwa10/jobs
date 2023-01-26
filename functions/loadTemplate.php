<?php
function loadTemplate($fileName, $templateVars)
{
    extract($templateVars);
    ob_start();
    require $fileName;
    return ob_get_clean();
}

function adminValidation(): void
{
    if (!isset($_SESSION)) {
        session_start();
    }
    if ($_SESSION['userDetails']['userType'] != 'admin') {
        header("Location: clientIndex");
        exit();
    }

}
