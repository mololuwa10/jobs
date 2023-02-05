<?php

namespace controller;

use Database\AdminValidation;
use Database\DatabaseTable;
use JetBrains\PhpStorm\NoReturn;

class AdminController
{
    private $get;

    private $post;
    /**
     * @var mixed|string
     */
    private $dbName;
    private $validation;
    public function __construct(array $get, array $post, $dbName = 'job')
    {
        $this->get = $get;
        $this->post = $post;
        $this->dbName = $dbName;
        $this->validation = new AdminValidation();
    }

    public function adminLogin(): array
    {
        $errMsgArray = [];
        $errorFlag = false;
        if (isset($this->post['submit'])) {
            $userName = $this->post['userName'];
            $password = $this->post['password'];

            if ($userName == '' || $password == '') {
                $errMsgArray[] = 'YOU MUST ENTER A VALID CREDENTIAL';
                $errorFlag = true;
            }
            $userAdminTable = new DatabaseTable('user', 'userId', $this->dbName);
            $user = $userAdminTable->find('userName', $userName);

            if ($user) {
                $_SESSION['loggedin'] = $user['userId'];
                $_SESSION['userDetails'] = $user;
                if (password_verify($password, $user['password']) && $userName == $user['userName']) {
                    $_SESSION['password_verified'] = true;
                    if ($user['userType'] == 'client') {
                        header("Location: clientIndex");
                    } elseif ($user['userType'] == 'admin') {
                        header("Location: adminIndex");
                    }
                } else {
                    $_SESSION['password_verified'] = false;
                    $errMsgArray[] = 'CREDENTIALS ARE WRONG!! TRY AGAIN';
                    $errorFlag = true;
                }
            }
        }
        return ['template' => '../templates/admin/adminLogin.html.php',
            'variables' => ['errMsgArray' => $errMsgArray],
            'title' => 'Jo\'s Jobs - Admin Login'
        ];
    }

    public function adminIndex()
    {
        $this->validation->adminValidation();

        return ['template' => '../templates/admin/adminIndex.html.php',
            'variables' => [],
            'title' => 'Jo\'s Jobs - Admin Home'
        ];
    }

    public function jobs(): array
    {
        $this->validation->adminValidation();
        $jobsTable = new DatabaseTable('job', 'id', $this->dbName);

        $categoryTable = new DatabaseTable('category', 'id', $this->dbName);
        $category = $categoryTable->findAll();

        $stmt = 'SELECT j.id, j.title, j.description, j.salary, j.categoryId, j.archive, (SELECT count(*) FROM applicants WHERE jobId = j.id) as count, c.id as catId, c.name
             FROM job j LEFT JOIN category c ON c.id = j.categoryId';

        $criteria = [];

        if (isset($this->get['id']) && $this->get["id"] != 'All') {
            $stmt .= ' WHERE j.categoryId = :id';
            $criteria = ['id' => $this->get['id']];
        }

        $jobs = $jobsTable->custom($stmt, $criteria, false);

        return ['template' => '../templates/admin/jobs.html.php',
            'variables' => ['jobs' => $jobs, 'category' => $category],
            'title' => 'Jo\'s Jobs - Job List'
        ];
    }

    public function addUser(): array
    {
        $this->validation->adminValidation();
        $errorMessageArray = [];
        $validationMessage = '';
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            if (isset($this->post['submit'])) {
                $fullName = $this->post['fullName'];
                $password = $this->post['password'];
                $userName = $this->post['username'];
                $userType = $this->post['userType'];

                $newAdminPassHash = password_hash($password, PASSWORD_DEFAULT);

                if (empty($fullName)) {
                    $errorMessageArray[] = "FULL NAME FIELD IS EMPTY";
                }
                if (empty($password)) {
                    $errorMessageArray[] = "PASSWORD FIELD IS EMPTY";
                }
                if (empty($userName)) {
                    $errorMessageArray[] = "USERNAME FIELD IS EMPTY";
                }

                if (empty($errorMessageArray)) {
                    $userAdminTable = new DatabaseTable('user', 'userId', $this->dbName);
                    $user = $userAdminTable->find('userName', $userName);
                    if ($user) {
                        $errorMessageArray[] = "CREDENTIALS ALREADY EXIST";
                    } else {
                        $criteria = [
                            'fullName' => $fullName,
                            'password' => $newAdminPassHash,
                            'userName' => $userName,
                            'userType' => $userType
                        ];
                        $addUser = $userAdminTable->insert($criteria);
                        $validationMessage = 'USER ADDED!';
                    }
                }
            }
        }
        return ['template' => '../templates/admin/addUser.html.php',
            'variables' => ['validationMessage' => $validationMessage, 'errorMessageArray' => $errorMessageArray],
            'title' => 'Jo\'s Jobs - Add User'
        ];
    }

    /**
     * @return array
     */
    public function manageUser(): array
    {
        $this->validation->adminValidation();
            $userTable = new DatabaseTable('user', 'userId', $this->dbName);
            $users = $userTable->findAll();
            return ['template' => '../templates/admin/manageUser.html.php',
                'variables' => ['users' => $users],
                'title' => 'Jo\'s Jobs - Manage User'
            ];
    }

    /**
     * @return void
     */
    public function deleteUser(): void
    {
        $userTable = new DatabaseTable('user', 'userId', $this->dbName);
        $user = $userTable->delete(['userId' => $this->post['id']]);
        header("Location: manageUser");
        exit();
    }

    public function logOut(): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        unset($_SESSION);
        session_destroy();
        header("Location: adminLogin");
    }

    public function addJob(): array
    {
        $categoriesTable = new DatabaseTable('category', 'id', $this->dbName);
        $categories = $categoriesTable->findAll();

        if (isset($this->post['submit'])) {
            $jobsTable = new DatabaseTable('job', 'id', $this->dbName);
            $criteria = [
                'title' => $this->post['title'],
                'description' => $this->post['description'],
                'salary' => $this->post['salary'],
                'location' => $this->post['location'],
                'categoryId' => $this->post['categoryId'],
                'closingDate' => $this->post['closingDate'],
                'userId' => $_SESSION['userDetails']['userId']
            ];
            $jobs = $jobsTable->save($criteria);
            echo 'Job Added';
        }

        $template = ($_SESSION['userDetails']['userType'] == 'admin') ? '../templates/admin/addjob.html.php' : '../templates/client/clientAddJob.html.php';
        $title = ($_SESSION['userDetails']['userType'] == 'admin') ? 'Jo\'s Jobs - Admin Add Jobs' : 'Jo\'s Jobs - Client Add Jobs';

        return ['template' => $template,
            'variables' => ['categories' => $categories],
            'title' => $title
        ];
    }

    public function editJob(): array
    {
        $jobsTable = new DatabaseTable('job', 'id', $this->dbName);
        if (isset($this->post['submit'])) {
            $criteria = [
                'title' => $this->post['title'],
                'description' => $this->post['description'],
                'salary' => $this->post['salary'],
                'location' => $this->post['location'],
                'categoryId' => $this->post['categoryId'],
                'closingDate' => $this->post['closingDate'],
                'id' => $this->post['id'],
            ];

            $updateJob = $jobsTable->update($criteria);
            echo 'Job saved';
        }

        $job = $jobsTable->find('id', $this->get['id']);

        $categoriesTable = new DatabaseTable('category', 'id', $this->dbName);
        $stmt = $categoriesTable->findAll();

        $template = ($_SESSION['userDetails']['userType'] == 'admin') ? '../templates/admin/editJob.html.php' : '../templates/client/clientEditJob.html.php';
        $title = ($_SESSION['userDetails']['userType'] == 'admin') ? 'Jo\'s Jobs - Admin Edit Jobs' : 'Jo\'s Jobs - Client Edit Jobs';

        return ['template' => $template,
            'variables' => ['job' => $job, 'stmt' => $stmt],
            'title' => $title
        ];
    }

    public function addCategory(): array
    {
        $this->validation->adminValidation();
        $message = '';
        if (isset($this->post['submit'])) {
            $categoriesTable = new DatabaseTable('category', 'id', $this->dbName);
            $criteria = [
                'name' => $this->post['name']
            ];
            $category = $categoriesTable->save($criteria);
            $message = 'Category added';
        }


        return ['template' => '../templates/admin/addcategory.html.php',
            'variables' => ['message' => $message],
            'title' => 'Jo\'s Jobs - Add Category'
        ];
    }

    public function archiveJob(): void
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $jobsTable = new DatabaseTable('job', 'id', $this->dbName);
            $r = ($this->post['archive'] == 1) ? "1" : "0";
            $archiveJob = $jobsTable->update(['archive' => $r, 'id' => $this->post['id']]);
            if ($_SESSION['userDetails']['userType'] == 'client') {
                header("Location: clientJobs");
            } else {
                header("Location: jobs");
            }
        }
    }

    public function categories(): array
    {
        $this->validation->adminValidation();
        $categoryTable = new DatabaseTable('category', 'id', $this->dbName);
        $categories = $categoryTable->findAll();

        return ['template' => '../templates/admin/adminCategories.html.php',
            'variables' => ['categories' => $categories],
            'title' => 'Jo\'s Jobs - Category'
        ];
    }

    public function deleteCategories(): void
    {
        $this->validation->adminValidation();
        $categoryTable = new DatabaseTable('category', 'id', $this->dbName);
        $deleteCategory = $categoryTable->delete(['id' => $this->post['id']]);
        header('location: categories');
    }

    public function applicants(): array
    {
        $jobsTable = new DatabaseTable('job', 'id', $this->dbName);
        $job = $jobsTable->find('id', $this->get['id']);

        $stmt = 'SELECT * FROM applicants WHERE jobId = :id';
        $applicants = $jobsTable->custom($stmt, ['id' => $this->get['id']], false);

        $template = ($_SESSION['userDetails']['userType'] == 'admin') ? '../templates/admin/applicants.html.php' : '../templates/client/clientsApplicants.html.php';
        $title = ($_SESSION['userDetails']['userType'] == 'admin') ? 'Jo\'s Jobs - Applicants' : 'Jo\'s Jobs - Client Applicants';

        return [
            'template' => $template,
            'variables' => ['job' => $job, 'applicants' => $applicants],
            'title' => $title
        ];
    }

    public function manageEnquiry(): array
    {
        $this->validation->adminValidation();

        $contactTable = new DatabaseTable('contact', 'id', $this->dbName);
        if ($this->post) {
            if (isset($this->post['responded']) && $this->post['responded'] == 'on') {
                $updateResponse = ['id' => $this->post['id'], 'userId' => $_SESSION['userDetails']['userId']];
            } else {
                $updateResponse = ['id' => $this->post['id'], 'userId' => NULL];
            }
            $contactTable->update($updateResponse);
        }

        $stmt = 'SELECT c.id, c.fullname, c.enquiry, c.email, c.phoneNumber, c.userId, u.userId as adminId
                 FROM contact c 
                 LEFT JOIN user u ON u.userId = c.userId';
        $criteria = [];

        $contacts = $contactTable->custom($stmt, $criteria, false);

        return ['template' => '../templates/admin/manageEnquiry.html.php',
            'variables' => ['contacts' => $contacts],
            'title' => 'Jo\'s Jobs - Enquiries List'
        ];
    }

    public function clientIndex()
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            return ['template' => '../templates/client/clientIndex.html.php',
                'variables' => [],
                'title' => 'Jo\'s Jobs - Client Home'
            ];
        } else {
            header("Location: ../home");
        }
    }

    public function clientJobs(): array
    {
        $jobsTable = new DatabaseTable('job', 'id', $this->dbName);

        $categoryTable = new DatabaseTable('category', 'id', $this->dbName);
        $category = $categoryTable->findAll();

        $stmt = 'SELECT j.id, j.title, j.description, j.salary, j.categoryId, j.archive, j.userId, (SELECT count(*) FROM applicants WHERE jobId = j.id) as count, c.id as catId, u.userId as userId, u.userType, c.name
                    FROM job j 
                    JOIN user u ON u.userId = j.userId
                    LEFT JOIN category c ON c.id = j.categoryId';

        $criteria = [];

        if (isset($_SESSION['userDetails']['userType']) && $_SESSION['userDetails']['userType'] == 'client') {
            $stmt .= ' WHERE j.userId = :userId';
            $criteria = [
                'userId' => $_SESSION['userDetails']['userId']
            ];
        } else {
            header("Location: ../home");
        }


        if (isset($this->get['id'])) {
            if ($_SESSION['userDetails']['userType'] == 'client') {
                $stmt .= ' AND j.categoryId = :id ';
            } else {
                $stmt .= ' WHERE j.categoryId = :id ';
            }
            $criteria['id'] = $this->get['id'];
        }
        $jobs = $jobsTable->custom($stmt, $criteria, false);

        return ['template' => '../templates/client/clientJobs.html.php',
            'variables' => ['jobs' => $jobs, 'category' => $category],
            'title' => 'Jo\'s Jobs - Client Home'
        ];
    }


    public function editCategory(): array
    {
        $this->validation->adminValidation();
        $categoriesTable = new DatabaseTable('category', 'id', $this->dbName);
        $currentCategory = $categoriesTable->find('id', $this->get['id']);

        if (isset($this->post['submit'])) {
            $updateCategory = $categoriesTable->update(
                [
                    'name' => $this->post['name'],
                    'id' => $this->post['id']
                ]
            );
            echo 'Category Saved';
        }

        return ['template' => '../templates/admin/editCategory.html.php',
            'variables' => ['currentCategory' => $currentCategory],
            'title' => 'Jo\'s Jobs - Client Home'
        ];
    }
}