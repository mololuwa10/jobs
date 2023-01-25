<?php
session_start();

use controller\PageController;
use Database\DatabaseTable;

require '../functions/loadTemplate.php';
require '../autoload.php';

$jobsTable = new DatabaseTable('job', 'id');
$pageController = new PageController();

if ($_SERVER['REQUEST_URI'] !== '/') {
    $functionName = ltrim(explode('?', $_SERVER['REQUEST_URI'])[0], '/');
    if (str_contains($functionName, '/')) {
        $r = explode('/', $functionName);
        $controller = ucfirst($r[0]) . 'Controller';
        $loadController = 'controller\\' . $controller;
        $pageController = new $loadController();
        $functionName = $r[1] ?? 'home';
    }
    $page = $pageController->$functionName();
} else {
    $page = $pageController->home();
}

$title = $page['title'];
$output = loadTemplate('../templates/' . $page['template'], $page['variables']);
require '../templates/layout/layout.html.php';

