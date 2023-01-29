<?php

use controller\AdminController;
use Database\DatabaseTable;
use functions\ManageTest;
use PHPUnit\Framework\TestCase;

class AdminControllerTest extends TestCase
{
//    public function testAddCategory(){
//        // validate a user - admin
//
//        $postData = [
//            'submit' => 'submit',
//            'name' => 'Test First Category'
//        ];
//
//        $adminController = new \controller\AdminController([], $postData, 'testJob');
//        $response = $adminController->addCategory();
//
//        // assert
//        $this->assertTrue($postData);
//        $this->assertEquals($response['variables']['message'], 'Category added');
//    }

    private $categoriesTable;
    private $dbName;
    private $usersTable;
    public function setUp()
    {
        $this->dbName = 'testDb';
        $this->categoriesTable = new DatabaseTable('category', 'id', 'testJob');
        $this->usersTable = new DatabaseTable('user', 'userId', 'testJob');
        $this->applicantsTable = new DatabaseTable('applicants', 'id', 'testJob');
        $this->contactTable = new DatabaseTable('contact', 'id', 'testJob');

    }


    public function testAdminIndex() {
        $user = [
            'fullName' => 'Test Name',
            'userName' => 'TestUserName',
            'password' => 'TestPassword',
            'userType' => 'admin',
        ];

        $_SESSION['loggedin'] = 1;
        $_SESSION['userDetails'] = $user;
        $adminController = new AdminController([], [], 'testJob');
        $response = $adminController->adminIndex();

        $this->assertEquals("../templates/admin/adminIndex.html.php", $response['template']);
        $this->assertEquals("Jo's Jobs - Admin Home", $response['title']);
    }

    public function testJobs() {
        $user = [
            'fullName' => 'Test Name',
            'userName' => 'TestUserName',
            'password' => 'TestPassword',
            'userType' => 'admin',
        ];

        $_SESSION['loggedin'] = 1;
        $_SESSION['userDetails'] = $user;

        $adminController = new AdminController(['id' => 1], [], 'testJob');
        $response = $adminController->jobs();

        $this->assertArrayHasKey('template', $response);
        $this->assertArrayHasKey('variables', $response);
        $this->assertArrayHasKey('title', $response);

        $this->assertEquals('../templates/admin/jobs.html.php', $response['template']);
        $this->assertArrayHasKey('jobs', $response['variables']);
        $this->assertArrayHasKey('category', $response['variables']);
        $this->assertEquals("Jo's Jobs - Job List", $response['title']);
    }

    public function testManageUser() {
        $user = [
            'fullName' => 'Test Name',
            'userName' => 'TestUserName',
            'password' => 'TestPassword',
            'userType' => 'admin',
        ];

        $_SESSION['loggedin'] = 1;
        $_SESSION['userDetails'] = $user;

        $adminController = new AdminController(['id' => 1], [], 'testJob');
        $response = $adminController->manageUser();

        $expectedTemplate = '../templates/admin/manageUser.html.php';
        $expectedTitle = "Jo's Jobs - Manage User";

        $this->assertArrayHasKey('template', $response);
        $this->assertArrayHasKey('variables', $response);
        $this->assertArrayHasKey('title', $response);

        $this->assertEquals($expectedTemplate, $response['template']);
        $this->assertArrayHasKey('users', $response['variables']);
        $this->assertEquals($expectedTitle, $response['title']);
    }

//    public function testDeleteUser() {
//        $truncateTable = new ManageTest();
//        $truncateTable->truncateTable();
//            $user = [
//                'fullName' => 'Test User',
//                'userName' => 'TestUsername',
//                'password' => 'TestPassword',
//                'userType' => 'admin',
//            ];
//            $this->usersTable->save($user);
//
//            $userCountBeforeDeletion = count($this->usersTable->findAll());
//
//            $adminController = new AdminController([], ['id' => 1], 'testJob');
//            $adminController->deleteUser();
//            $userCountAfterDeletion = count($this->usersTable->findAll());
//
//            $this->assertEquals($userCountBeforeDeletion - 1, $userCountAfterDeletion);
//        }

    public function testAddJob() {
        $postData = [
            'title' => 'Test Job Title',
            'description' => 'Test Job Description',
            'salary' => '1000',
            'location' => 'Test Location',
            'categoryId' => '1',
            'closingDate' => '2023-02-01',
            'submit' => true,
        ];
        $_SESSION['userDetails'] = [
            'userId' => '1',
            'userType' => 'admin',
        ];
        $adminController = new AdminController([], $postData, 'testJob');

        $response = $adminController->addJob();

        $this->assertArrayHasKey('template', $response);
        $this->assertArrayHasKey('variables', $response);
        $this->assertArrayHasKey('title', $response);

        $this->assertEquals('../templates/admin/addjob.html.php', $response['template']);
        $this->assertArrayHasKey('categories', $response['variables']);
        $this->assertEquals("Jo's Jobs - Admin Add Jobs", $response['title']);
    }

    public function testEditJob() {
        $_SESSION['userDetails']['userType'] = 'admin';

        $postData = [
            'submit' => true,
            'title' => 'Test Job',
            'description' => 'Test description',
            'salary' => '100000',
            'location' => 'Test Location',
            'categoryId' => '1',
            'closingDate' => '2022-12-31',
            'id' => '1',
        ];
        $getData= ['id' => '1'];

        $adminController = new AdminController($getData, $postData, 'testJob');

        $result = $adminController->editJob();

        $this->assertArrayHasKey('variables', $result);
        $this->assertArrayHasKey('job', $result['variables']);
        $this->assertArrayHasKey('stmt', $result['variables']);
    }

    public function testAddCategory()
    {
        $truncateTable = new ManageTest();
        $truncateTable->truncateTable();

        $postData = [
            'submit' => true,
            'name' => 'Testing Category'
        ];

        $user = [
            'fullName' => 'Test Name',
            'userName' => 'TestUserName',
            'password' => 'TestPassword',
            'userType' => 'admin',
        ];

        $_SESSION['loggedin'] = 1;
        $_SESSION['userDetails'] = $user;

        $adminController = new AdminController([], $postData, 'testJob');
        $response = $adminController->addCategory();
        $this->assertEquals($response['variables']['message'], 'Category added');
    }

    public function testAddUser()
    {
//        $ManageTest = new ManageTest();
//        $ManageTest->truncateTable();
//
//        $ManageTest->register(['fullName' => 'Test Name', 'userName' =>'Testname', 'password' => 'testpassword', 'userType' => 'admin']);

        $errorMessageArray = [];
        $validationMessage = '';

        $postData = [
            'submit' => 'submit',
            'fullName' => 'Lionel Messi',
            'password' => 'lionel',
            'username' => 'Lionel',
            'userType' => 'admin'
        ];

        $adminController = new AdminController([], $postData, 'testJob');
        $response = $adminController->addUser();

        $errorMessageArray = $response['variables']['errorMessageArray'];
        $validationMessage = $response['variables']['validationMessage'];

        $this->assertEquals('../templates/admin/addUser.html.php', $response['template']);
        $this->assertEquals('Jo\'s Jobs - Add User', $response['title']);

        if (empty($errorMessageArray)) {
            $this->assertEquals('USER ADDED!', $validationMessage);
        } else {
            $this->assertEquals(["CREDENTIALS ALREADY EXIST"], $errorMessageArray);

        }
    }

//    public function testArchiveJob() {
//        $_SESSION['loggedin'] = true;
//        $_SESSION['userDetails']['userType'] = 'client';
//
//        $postData = [
//            'archive' => 1,
//            'id' => 1,
//        ];
//        $adminController = new AdminController([], $postData, 'testJob');
//
//        ob_start();
//        $adminController->archiveJob();
//        $clientLocation = ob_get_clean();
//
//        $_SESSION['userDetails']['userType'] = 'admin';
//        ob_start();
//        $this->controller->archiveJob();
//        $location = ob_get_clean();
//
//        $this->assertEquals('Location: jobs', $location);
//        $this->assertEquals('Location: clientJobs', $clientLocation);
//    }

        public function testCategories() {
            $truncateTable = new ManageTest();
            $truncateTable->truncateTable();

            $user = [
                'fullName' => 'Test Name',
                'userName' => 'TestUserName',
                'password' => 'TestPassword',
                'userType' => 'admin',
            ];

            $_SESSION['loggedin'] = 1;
            $_SESSION['userDetails'] = $user;

            $adminController = new AdminController([], [], 'testJob');
            $response = $adminController->categories();

            $this->assertArrayHasKey('variables', $response);
            $this->assertArrayHasKey('categories', $response['variables']);
        }

        public function testApplicants() {
            $getApplicants = [
                'name' => 'Test Applicant Name',
                'email' => 'testapplicant@gmail.com',
                'details' => 'test applicants details',
                'id' => 1
            ];
            $this->applicantsTable->save($getApplicants);

            $adminController = new AdminController($getApplicants, [], 'testJob');
            $result = $adminController->applicants();
            if($_SESSION['userDetails']['userType'] = 'admin' && $_SESSION['userDetails']['userType'] = 'client') {
                $this->assertArrayHasKey('variables', $result);
                $this->assertArrayHasKey('job', $result['variables']);
                $this->assertArrayHasKey('applicants', $result['variables']);
            }
        }
        public function testManageEnquiry() {
            $user = [
                'fullName' => 'Test Name',
                'userName' => 'TestUserName',
                'password' => 'TestPassword',
                'userType' => 'admin',
            ];

            $_SESSION['loggedin'] = 1;
            $_SESSION['userDetails'] = $user;

            $contactPostData = [
                'fullname' => 'Test Contact Name',
                'email' => 'testContact@gmail.com',
                'enquiry' => 'test contact enquiry',
                'phoneNumber' => '07473143014',
                'userId' => 1,
                'id' => 1
            ];
            $this->contactTable->save($contactPostData);

            $postData = [
                'id' => 1,
                'responded' => 'on'
            ];
            $_SESSION['userDetails']['userId'] = 1;

            $adminController = new AdminController([], $postData, 'testJob');
            $result = $adminController->manageEnquiry();

            $this->assertArrayHasKey('id', $result['variables']['contacts'][0]);
            $this->assertArrayHasKey('fullname', $result['variables']['contacts'][0]);
            $this->assertArrayHasKey('enquiry', $result['variables']['contacts'][0]);
            $this->assertArrayHasKey('email', $result['variables']['contacts'][0]);
            $this->assertArrayHasKey('phoneNumber', $result['variables']['contacts'][0]);
            $this->assertArrayHasKey('userId', $result['variables']['contacts'][0]);
            $this->assertArrayHasKey('adminId', $result['variables']['contacts'][0]);
            $this->assertEquals('Jo\'s Jobs - Enquiries List', $result['title']);

            $postData = [
                'id' => 1,
                'responded' => ''
            ];
            $_SESSION['userDetails']['userId'] = 1;

            $adminController = new AdminController([], $postData, 'testJob');
            $result = $adminController->manageEnquiry();

            $this->assertArrayHasKey('id', $result['variables']['contacts'][0]);
            $this->assertArrayHasKey('fullname', $result['variables']['contacts'][0]);
            $this->assertArrayHasKey('enquiry', $result['variables']['contacts'][0]);
            $this->assertArrayHasKey('email', $result['variables']['contacts'][0]);
            $this->assertArrayHasKey('phoneNumber', $result['variables']['contacts'][0]);
            $this->assertArrayHasKey('userId', $result['variables']['contacts'][0]);
            $this->assertArrayHasKey('adminId', $result['variables']['contacts'][0]);
            $this->assertEquals('Jo\'s Jobs - Enquiries List', $result['title']);
        }

        public function testClientIndex() {
            $user = [
                'fullName' => 'Test Name Client',
                'userName' => 'TestUserNameClient',
                'password' => 'TestPassword',
                'userType' => 'client',
            ];

            $_SESSION['loggedin'] = 1;
            $_SESSION['userDetails'] = $user;
            $adminController = new AdminController([], [], 'testJob');
            $response = $adminController->clientIndex();

            $this->assertEquals("../templates/client/clientIndex.html.php", $response['template']);
            $this->assertEquals("Jo's Jobs - Client Home", $response['title']);
        }

        public function testEditCategory() {
            $truncateTable = new ManageTest();
            $truncateTable->truncateTable();

            $user = [
                'fullName' => 'Test Name',
                'userName' => 'TestUserName',
                'password' => 'TestPassword',
                'userType' => 'admin',
            ];

            $_SESSION['loggedin'] = 1;
            $_SESSION['userDetails'] = $user;

            $postData = [
                'submit' => true,
                'id' => 1,
                'name' => 'New Test Category'
            ];

            $adminController = new AdminController(['id' => 1], $postData, 'testJob');
            $response = $adminController->editCategory();

            $this->assertArrayHasKey('currentCategory', $response['variables']);
            $this->assertArrayHasKey('id', $response['variables']['currentCategory']);
            $this->assertArrayHasKey('name', $response['variables']['currentCategory']);
            $this->assertEquals(1, $response['variables']['currentCategory']['id']);
            $this->assertEquals($postData['name'], $response['variables']['currentCategory']['name']);
        }

//    public function validateAdmin() {
//        // simulate starting a session
//        $_SESSION = array();
//        $_SESSION['userDetails'] = array('userType' => 'admin');
//        if (!isset($_SESSION)) {
//            session_start();
//        }
//        $this->assertArrayHasKey('userDetails', $_SESSION);
//        $this->assertEquals('admin', $_SESSION['userDetails']['userType']);
//    }
}