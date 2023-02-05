<?php

use Database\DatabaseTable;
use functions\ManageTest;
use controller\PageController;
use PHPUnit\Framework\TestCase;

/**
 * @covers controller\PageController
 */
class PageControllerTest extends TestCase
{
    private $categoriesTable;
    private $jobsTable;
    private $contactTable;
    private $applicantsTable;
    private $date;
    private $fileName;
    private $extension;
    private $parts;

    protected function setUp(): void
    {
        $this->pdo = new \PDO('mysql:dbname=testJob;host=mysql', 'student', 'student');
        $this->categoriesTable = new DatabaseTable('category', 'id', 'testJob');
        $this->jobsTable = new DatabaseTable('job', 'id', 'testJob');
        $this->contactTable = new DatabaseTable('contact', 'id', 'testJob');
        $this->applicantsTable = new DatabaseTable('applicants', 'id', 'testJob');
        $this->date = new DateTime();
        $this->fileName = uniqid() . '.pdf';
    }

    public function testCategories()
    {
        $manageTest = new ManageTest();
        $getData = ['id' => 1];

        $PageController = new PageController($getData, [], 'testJob');
        $date = new DateTime();

        $result = $PageController->categories();

        $this->assertArrayHasKey('jobs', $result['variables']);
        $this->assertArrayHasKey('categories', $result['variables']);
        $this->assertArrayHasKey('currentCategory', $result['variables']);
    }
    public function testHome() {
        $manageTest = new ManageTest();
        $this->jobsTable->delete([]);
        $this->jobsTable->insert([
            'title' => 'Teaching Assistant',
            'description' => 'This is a teaching Job',
            'salary' => '£30,000 - £40,000',
            'closingDate' => '2023-01-08',
            'categoryId' => 1,
            'location' => 'Birmingham'
        ]);

        $pageController = new \controller\PageController([], [], 'testJob');
        $getData = [
            'locations' => 'Birmingham'
        ];
        $response = $pageController->home();
        $manageTest->addJob($getData);
        $manageTest->addJob();

        $this->assertTrue(is_array($response));
        $this->assertEquals($response['variables']['locations'][0]['location'], $getData['locations']);
    }

    public function testFilter() {
        $manageTest = new ManageTest();
        $this->jobsTable->delete([]);

        $getData = [
            'locations' => 'New York'
        ];
        $manageTest->addJob($getData);
        for($i = 1; $i < 6; $i++) {
            $manageTest->addJob();
        }

        $pageController = new \controller\PageController([], [], 'testJob');
        $response = $pageController->home();

        $this->assertTrue(1 == count($response['variables']['locations']));
        $this->assertTrue(6 == count($response['variables']['jobs']));
        $filterPageController = new PageController(['location' => $getData['locations']] , [], 'testJob');
        $filterResponse = $filterPageController->home();
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

    public function testAboutUs() {
        $this->categoriesTable->delete([]);

        $this->categoriesTable->insert(
            [
            'id' => 1,
            'name' => 'Test Category1'
            ]
        );
        $this->categoriesTable->insert(
            [
                'id' => 2,
                'name' => 'Test Category2'
            ]
        );

        $pageController = new PageController([], [],  'testJob');
        $response = $pageController->aboutUs();

        $this->assertArrayHasKey('variables', $response);
        $this->assertArrayHasKey('categories', $response['variables']);
        $this->assertCount(2, $response['variables']['categories']);
    }

    public function testContact()
    {
        $errorMessage = [];
        $validationMessage = '';
        $this->contactTable->delete([]);
        $postData = [
            'fullname' => 'Test Contact Name',
            'email' => 'testContact@gmail.com',
            'enquiry' => 'test contact enquiry',
            'phoneNumber' => 07473143014,
            'submit' => true
        ];

        $pageController = new PageController([], $postData, 'testJob');
        ob_start();
        $response = $pageController->contact();
        ob_get_clean();

        $errorMessage = $response['variables']['errorMessage'];
        $validationMessage = $response['variables']['validationMessage'];

        $this->assertEquals('ENQUIRY RECEIVED', $validationMessage);
        $contact = $this->contactTable->find('email', 'testContact@gmail.com');
        $this->assertNotEmpty($contact);

        $this->contactTable->delete([]);
        $postData = [
            'fullname' => '',
            'email' => '',
            'enquiry' => '',
            'phoneNumber' => '',
            'submit' => true
        ];

        $pageController = new PageController([], $postData, 'testJob');
        ob_start();
        $response = $pageController->contact();
        $output = ob_get_clean();

        $errorMessage = $response['variables']['errorMessage'];
        $validationMessage = $response['variables']['validationMessage'];
        if(empty($errorMessage)) {
            $this->assertEquals('EVERY FIELD IS NOT FIELD', $output);
        }

        $this->contactTable->delete([]);
        $criteria = [
            'fullname' => 'Test Contact Name',
            'email' => 'testContact@gmail.com',
            'enquiry' => 'test contact enquiry',
            'phoneNumber' => 07473143014,
        ];
        $this->contactTable->insert($criteria);

        $postData = [
            'fullname' => 'Test Contact Name',
            'email' => 'testContact@gmail.com',
            'enquiry' => 'test contact enquiry',
            'phoneNumber' => 07473143014,
            'submit' => true
        ];
        $pageController = new PageController([], $postData, 'testJob');
        ob_start();
        $response = $pageController->contact();
        ob_get_clean();

        $errorMessage = $response['variables']['errorMessage'];
        $validationMessage = $response['variables']['validationMessage'];

        $this->assertEquals(["CREDENTIALS ALREADY EXIST"], $errorMessage);
    }

    public function testApply() {
        $this->applicantsTable->delete([]);
        $postData = [
            'name' => 'Segilola Mololuwa',
            'email' => 'segilolamololuwa@gmail.com',
            'details' => 'Test details',
            'jobId' => 1,
            'submit' => 'submit'
        ];

        $getData = [
            'id' => 1
        ];
        $pageController = new PageController($getData, $postData, 'testJob');
        $file = [
            'name' => $this->fileName,
            'type' => 'text/plain',
            'tmp_name' => '/tmp/' . $this->fileName,
            'error' => 0,
            'size' => 123,
        ];
        $_FILES['cv'] = $file;
        $response = $pageController->apply();
        $this->assertArrayHasKey('template', $response);
        $this->assertArrayHasKey('variables', $response);
        $this->assertArrayHasKey('title', $response);
        $this->assertArrayHasKey('job', $response['variables']);
        $this->assertArrayHasKey('categories', $response['variables']);
        $this->assertArrayHasKey('errorMessage', $response['variables']);
        $this->assertEquals('Your application is complete. We will contact you after the closing date.', $response['variables']['errorMessage'][0]);
        $this->applicantsTable->delete([]);
        $postData = [
            'name' => 'Segilola Michael',
            'email' => 'segilolamichael@gmail.com',
            'details' => 'Test details',
            'jobId' => 1,
            'submit' => 'submit'
        ];

        $getData = [
            'id' => 2
        ];
        $pageController = new PageController($getData, $postData, 'testJob');

        $fileNotExisting = [
            'name' => $this->fileName,
            'type' => 'text/plain',
            'tmp_name' => '/tmp/' . $this->fileName,
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => 123,
        ];
        $_FILES['cv'] = $fileNotExisting;
        $response = $pageController->apply();
        $this->assertArrayHasKey('template', $response);
        $this->assertArrayHasKey('variables', $response);
        $this->assertArrayHasKey('title', $response);
        $this->assertArrayHasKey('job', $response['variables']);
        $this->assertArrayHasKey('categories', $response['variables']);
        $this->assertArrayHasKey('errorMessage', $response['variables']);
        $this->assertEquals('There was an error uploading your CV', $response['variables']['errorMessage'][0]);
    }
}
