<?php
function loadTemplate($fileName, $templateVars)
{
    extract($templateVars);
    ob_start();
    require $fileName;
    return ob_get_clean();
}

