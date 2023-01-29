<?php

use Database\DatabaseTable;
use functions\ManageTest;
use controller\PageController;

/**
 * @covers controller\PageController
 */
class PageControllerTest extends \PHPUnit\Framework\TestCase
{
    private $categoriesTable;
    private $jobsTable;
    private $contactTable;
    private $applicantsTable;
    private $date;

    public function testCategories()
    {
        $categories = $this->categoriesTable->findAll();
        $this->assertTrue(is_array($categories));
    }

    public function testFindJobsByCategoryAndDate()
    {
        $criteria = [
            'date' => $this->date->format('Y-m-d'),
            'id' => 1
        ];

        $jobs = $this->jobsTable->customFind('categoryId = :id AND closingDate > :date', $criteria);
        $this->assertTrue(is_array($jobs));
    }

    public function testHome() {
        $manageTest = new ManageTest();
        $manageTest->truncateTable();

        $pageController = new \controller\PageController([], [], 'testJob');
        $getData = [
            'locations' => 'Test Location'
        ];
        $response = $pageController->home();
        $manageTest->addJob($getData);
        $manageTest->addJob();

        $this->assertTrue(is_array($response));
        $this->assertEquals($response['variables']['locations'][0]['location'], $getData['locations']);
    }

    public function testFilter() {
        $manageTest = new ManageTest();
        $manageTest->truncateTable();

        $getData = [
            'locations' => 'New York'
        ];
        $manageTest->addJob($getData);
        for($i = 1; $i < 6; $i++) {
            $manageTest->addJob();
        }
        $manageTest->addJob();

        $pageController = new \controller\PageController([], [], 'testJob');
        $response = $pageController->home();

        $this->assertTrue(2 == count($response['variables']['locations']));
//        $this->assertTrue(6 == count($response['variables']['jobs']));

        $filterPageController = new PageController(['location' => $getData['locations']] , [], 'testJob');
        $filterResponse = $filterPageController->home();

//        $this->assertTrue(1 == );
    }
    public function testFaqs()
    {
        $pageController = new PageController([], [], 'testJob');
        $expected = ['template' => '../templates/layout/faqs.html.php',
            'variables' => [],
            'title' => 'Jo\'s Jobs - FAQs'
        ];
        $response = $pageController->faqs();
        $this->assertEquals($expected, $response);
    }

    protected function setUp(): void
    {
        $this->pdo = new \PDO('mysql:dbname=testJob;host=mysql', 'student', 'student');
        $this->categoriesTable = new DatabaseTable('category', 'id', 'testJob');
        $this->jobsTable = new DatabaseTable('job', 'id', 'testJob');
        $this->contactTable = new DatabaseTable('contact', 'id', 'testJob');
        $this->applicantsTable = new DatabaseTable('applicants', 'id', 'testJob');
        $this->date = new DateTime();
    }
}
