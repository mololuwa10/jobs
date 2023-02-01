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


    /**
     * @throws Exception
     * @runInSeparateProcess
     */
    // https://stackoverflow.com/questions/14152608/headers-already-sent-error-returned-during-phpunit-tests
    public function testAdminIndex()
    {
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

    /**
     * @throws Exception
     * @runInSeparateProcess
     */
    // https://stackoverflow.com/questions/14152608/headers-already-sent-error-returned-during-phpunit-tests
    public function testJobs()
    {
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
    }

    /**
     * @throws Exception
     * @runInSeparateProcess
     */
    // https://stackoverflow.com/questions/14152608/headers-already-sent-error-returned-during-phpunit-tests
    public function testManageUser()
    {
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

    /**
     * @throws Exception
     * @runInSeparateProcess
     */
    // https://stackoverflow.com/questions/14152608/headers-already-sent-error-returned-during-phpunit-tests
    public function testDeleteUser()
    {
        $this->usersTable->delete([]);
        $user = [
            'fullName' => 'Test User',
            'userName' => 'TestUsername',
            'password' => 'TestPassword',
            'userType' => 'admin',
            'id' => 1
        ];
        $this->usersTable->save($user);

//        $userCountBeforeDeletion = count($this->usersTable->findAll());

        $adminController = new AdminController([], ['id' => 1], 'testJob');
        $adminController->deleteUser();
        $user = $this->usersTable->find(['userId' => $user['id']]);
        $this->assertEmpty($user);
//        $userCountAfterDeletion = count($this->usersTable->findAll());
//        $this->assertEquals($userCountBeforeDeletion - 1, $userCountAfterDeletion);
    }

    public function testAddJob()
    {
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

    public function testEditJob()
    {
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
        $getData = ['id' => '1'];

        $adminController = new AdminController($getData, $postData, 'testJob');

        $result = $adminController->editJob();

        $this->assertArrayHasKey('variables', $result);
        $this->assertArrayHasKey('job', $result['variables']);
        $this->assertArrayHasKey('stmt', $result['variables']);
    }

    /**
     * @throws Exception
     * @runInSeparateProcess
     */
    // https://stackoverflow.com/questions/14152608/headers-already-sent-error-returned-during-phpunit-tests
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

    /**
     * @throws Exception
     * @runInSeparateProcess
     */
    // https://stackoverflow.com/questions/14152608/headers-already-sent-error-returned-during-phpunit-tests
    public function testAddUser()
    {
        $this->usersTable->delete([]);
        $errorMessageArray = [];
        $validationMessage = '';

        $user = [
            'fullName' => 'Test Name',
            'userName' => 'TestUserName',
            'password' => 'TestPassword',
            'userType' => 'admin',
        ];

        $_SESSION['loggedin'] = 1;
        $_SESSION['userDetails'] = $user;

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

        if (empty($errorMessageArray)) {
            $this->assertEquals('USER ADDED!', $validationMessage);
        } else {
            $this->assertEquals(["CREDENTIALS ALREADY EXIST"], $errorMessageArray);
        }
    }

    public function testAddUserFail()
    {
        $this->usersTable->delete([]);
        $user = [
            'fullName' => 'Test Name Admin',
            'userName' => 'TestUserName',
            'password' => 'TestPassword',
            'userType' => 'admin',
        ];
        $_SESSION['loggedin'] = 1;
        $_SESSION['userDetails'] = $user;

        $postData = [
            'submit' => 'submit',
            'fullName' => '',
            'password' => '',
            'username' => '',
            'userType' => 'admin'
        ];

        $adminController = new AdminController([], $postData, 'testJob');
        $response = $adminController->addUser();

        $errorMessageArray = $response['variables']['errorMessageArray'];

        $this->assertContains("FULL NAME FIELD IS EMPTY", $response['variables']['errorMessageArray']);
        $this->assertContains("PASSWORD FIELD IS EMPTY", $response['variables']['errorMessageArray']);
        $this->assertContains("USERNAME FIELD IS EMPTY", $response['variables']['errorMessageArray']);
    }

    public function testAddUserExists()
    {
        $this->usersTable->delete([]);
        $user = [
            'fullName' => 'Test Name Admin',
            'userName' => 'TestUserName',
            'password' => 'TestPassword',
            'userType' => 'admin',
        ];
        $_SESSION['loggedin'] = 1;
        $_SESSION['userDetails'] = $user;

        $postData = [
            'submit' => 'submit',
            'fullName' => 'Lionel Messi',
            'password' => 'messi',
            'username' => 'messi',
            'userType' => 'admin'
        ];
        $adminController = new AdminController([], $postData, 'testJob');
        $response = $adminController->addUser();
        $response = $adminController->addUser();
        $this->assertEquals(["CREDENTIALS ALREADY EXIST"], $response['variables']['errorMessageArray']);
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

    /**
     * @return void
     * @runInSeparateProcess
     */
    // https://stackoverflow.com/questions/14152608/headers-already-sent-error-returned-during-phpunit-tests
    public function testCategories()
    {
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

    public function testApplicants()
    {
        $getApplicants = [
            'name' => 'Test Applicant Name',
            'email' => 'testapplicant@gmail.com',
            'details' => 'test applicants details',
            'id' => 1
        ];
        $this->applicantsTable->save($getApplicants);

        $adminController = new AdminController($getApplicants, [], 'testJob');
        $result = $adminController->applicants();
        if ($_SESSION['userDetails']['userType'] = 'admin' && $_SESSION['userDetails']['userType'] = 'client') {
            $this->assertArrayHasKey('variables', $result);
            $this->assertArrayHasKey('job', $result['variables']);
            $this->assertArrayHasKey('applicants', $result['variables']);
        }
    }

    /**
     * @return void
     * @runInSeparateProcess
     */
    // https://stackoverflow.com/questions/14152608/headers-already-sent-error-returned-during-phpunit-tests
    public function testManageEnquiry()
    {
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

    /**
     * @return void
     * @runInSeparateProcess
     */
    // https://stackoverflow.com/questions/14152608/headers-already-sent-error-returned-during-phpunit-tests
    public function testClientIndex()
    {
        $_SESSION['loggedin'] = true;
        $adminController = new AdminController([], [], 'testJob');
        $response = $adminController->clientIndex();

        $this->assertEquals("../templates/client/clientIndex.html.php", $response['template']);
        $this->assertEquals("Jo's Jobs - Client Home", $response['title']);
        $this->assertEquals([], $response['variables']);

        unset($_SESSION['loggedin']);
        ob_start();
        $response = $adminController->clientIndex();
        $result = ob_get_clean();

        $this->assertEmpty($result);
        $this->assertArrayNotHasKey('loggedin', $_SESSION);
    }

    /**
     * @return void
     * @runInSeparateProcess
     */
    // https://stackoverflow.com/questions/14152608/headers-already-sent-error-returned-during-phpunit-tests
    public function testEditCategory()
    {
        $user = [
            'fullName' => 'Test Name',
            'userName' => 'TestUserName',
            'password' => 'TestPassword',
            'userType' => 'admin',
        ];

        $_SESSION['loggedin'] = 1;
        $_SESSION['userDetails'] = $user;

        $this->categoriesTable->delete([]);
        $this->categoriesTable->insert(['name' => 'Test Category']);
        $id = $this->categoriesTable->find('name', 'Test Category')['id'];

        $postData = [
            'submit' => 'submit',
            'id' => $id,
            'name' => 'New Test Category'
        ];

        $adminController = new AdminController(['id' => $id], $postData, 'testJob');
        $response = $adminController->editCategory();

        $this->assertArrayHasKey('currentCategory', $response['variables']);
        $this->assertArrayHasKey('id', $response['variables']['currentCategory']);
        $this->assertArrayHasKey('name', $response['variables']['currentCategory']);
        $this->assertEquals($postData['id'], $response['variables']['currentCategory']['id']);
//            $this->assertEquals($postData['name'], $response['variables']['currentCategory']['name']);
        var_dump($response['variables']['currentCategory']['name']);
    }

    public function testClientJobs()
    {
        $_SESSION['userDetails']['userType'] = 'client';
        $_SESSION['userDetails']['userId'] = 1;

        $adminController = new AdminController(['id' => 1], [], 'testJob');
        $response = $adminController->clientJobs();

        $this->assertArrayHasKey('template', $response);
        $this->assertArrayHasKey('variables', $response);
        $this->assertArrayHasKey('title', $response);
        $this->assertArrayHasKey('jobs', $response['variables']);
        $this->assertArrayHasKey('category', $response['variables']);
    }

}