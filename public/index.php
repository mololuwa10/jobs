<?php
session_start();
require '../functions/loadTemplate.php';
require '../controller/PageController.php';

$jobsTable = new databaseTable('job', 'id');
$pageController = new PageController();

if ($_SERVER['REQUEST_URI'] !== '/') {
    $functionName = ltrim(explode('?', $_SERVER['REQUEST_URI'])[0], '/');
    if (str_contains($functionName, '/')) {
        $r = explode('/', $functionName);
        $controller = ucfirst($r[0]) . 'Controller';
        require "../controller/$controller.php";
        $pageController = new $controller();
        $functionName = $r[1] ?? 'home';
    }
    $page = $pageController->$functionName();
} else {
    $page = $pageController->home();
}

$title = $page['title'];
$output = loadTemplate('../templates/' . $page['template'], $page['variables']);

require '../templates/layout/layout.html.php';
?>