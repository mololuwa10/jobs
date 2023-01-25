<?php

namespace controller;

use Database\DatabaseTable;

class AdminController
{
    public function adminLogin(): array
    {
        $errMsgArray = [];
        $errorFlag = false;
        if (isset($_POST['submit'])) {
            $userName = $_POST['username'];
            $password = $_POST['password'];

            if ($userName == '' || $password == '') {
                $errMsgArray[] = 'YOU MUST ENTER A VALID CREDENTIAL';
                $errorFlag = true;
            }

            $userAdminTable = new DatabaseTable('user', 'userId');
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
                    echo 'CREDENTIALS ARE WRONG!! TRY AGAIN';
                    $errorFlag = true;
                }
            }

        }
        return ['template' => '../templates/admin/adminLogin.html.php',
            'variables' => [],
            'title' => 'Jo\'s Jobs - Admin Login'
        ];
    }

    public function adminIndex(): array
    {
        adminValidation();
        return ['template' => '../templates/admin/adminIndex.html.php',
            'variables' => [],
            'title' => 'Jo\'s Jobs - Admin Home'
        ];
    }

    public function jobs(): array
    {
        adminValidation();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $jobsTable = new DatabaseTable('job', 'id');

            $categoryTable = new DatabaseTable('category', 'id');
            $category = $categoryTable->findAll();

            $stmt = 'SELECT j.id, j.title, j.description, j.salary, j.categoryId, j.archive, (SELECT count(*) FROM applicants WHERE jobId = j.id) as count, c.id as catId, c.name
        FROM job j LEFT JOIN category c ON c.id = j.categoryId';

            $criteria = [];

            if (isset($_GET['id'])) {
                $stmt .= ' WHERE j.categoryId = :id';
                $criteria = ['id' => $_GET['id']];
            }

            $jobs = $jobsTable->custom($stmt, $criteria, false);

        }
        return ['template' => '../templates/admin/jobs.html.php',
            'variables' => ['jobs' => $jobs, 'category' => $category],
            'title' => 'Jo\'s Jobs - Job List'
        ];
    }

    public function addUser(): array
    {
        adminValidation();
        $errorMessageArray = [];
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            if (isset($_POST['submit'])) {
                $fullName = $_POST['fullName'];
                $password = $_POST['password'];
                $userName = $_POST['username'];
                $userType = $_POST['userType'];

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
                    $userAdminTable = new DatabaseTable('user', 'userId');
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
                        echo 'USER ADDED!';
                    }
                }
            }
        }
        return ['template' => '../templates/admin/addUser.html.php',
            'variables' => [],
            'title' => 'Jo\'s Jobs - Add User'
        ];
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
        adminValidation();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $categoriesTable = new DatabaseTable('category', 'id');
            $categories = $categoriesTable->findAll();

            if (isset($_POST['submit'])) {
                $jobsTable = new DatabaseTable('job', 'id');
                $criteria = [
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'salary' => $_POST['salary'],
                    'location' => $_POST['location'],
                    'categoryId' => $_POST['categoryId'],
                    'closingDate' => $_POST['closingDate'],
                ];
                $jobs = $jobsTable->save($criteria);
                echo 'Job Added';
            }
        }

        return ['template' => '../templates/admin/addjob.html.php',
            'variables' => ['categories' => $categories],
            'title' => 'Jo\'s Jobs - Add Job'
        ];
    }

    public function editJob(): array
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $jobsTable = new DatabaseTable('job', 'id');
            if (isset($_POST['submit'])) {
                $criteria = [
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'salary' => $_POST['salary'],
                    'location' => $_POST['location'],
                    'categoryId' => $_POST['categoryId'],
                    'closingDate' => $_POST['closingDate'],
                    'id' => $_POST['id']
                ];

                $updateJob = $jobsTable->update($criteria);
                echo 'Job saved';
            }

            $job = $jobsTable->find('id', $_GET['id']);

            $categoriesTable = new DatabaseTable('category', 'id');
            $stmt = $categoriesTable->findAll();

            $template = ($_SESSION['userDetails']['userType'] == 'admin') ? '../templates/admin/editJob.html.php' : '../templates/client/clientEditJob.html.php';
            $title = ($_SESSION['userDetails']['userType'] == 'admin') ? 'Jo\'s Jobs - Admin Edit Jobs' : 'Jo\'s Jobs - Client Edit Jobs';
        }

        return ['template' => $template,
            'variables' => ['job' => $job, 'stmt' => $stmt],
            'title' => $title
        ];
    }

    public function addCategory(): array
    {
        adminValidation();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            if (isset($_POST['submit'])) {
                $categoriesTable = new DatabaseTable('category', 'id');
                $criteria = [
                    'name' => $_POST['name']
                ];
                $category = $categoriesTable->save($criteria);

                echo 'Category added';
            }
        }

        return ['template' => '../templates/admin/addcategory.html.php',
            'variables' => [],
            'title' => 'Jo\'s Jobs - Add Category'
        ];
    }

    public function archiveJob(): void
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $jobsTable = new DatabaseTable('job', 'id');
            $r = ($_POST['archive'] == 1) ? "1" : "0";
            $archiveJob = $jobsTable->update(['archive' => $r, 'id' => $_POST['id']]);
            if ($_SESSION['userDetails']['userType'] == 'client') {
                header("Location: clientJobs");
            } else {
                header("Location: jobs");
            }
        }
    }

    public function categories(): array
    {
        adminValidation();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $categoryTable = new DatabaseTable('category', 'id');
            $categories = $categoryTable->findAll();
        }

        return ['template' => '../templates/admin/adminCategories.html.php',
            'variables' => ['categories' => $categories],
            'title' => 'Jo\'s Jobs - Category'
        ];
    }

    public function deleteCategories(): void
    {
        adminValidation();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $categoryTable = new DatabaseTable('category', 'id');
            $deleteCategory = $categoryTable->custom('DELETE FROM category WHERE id = :id', ['id' => $_POST['id']], true);

            header('location: categories');
        }
    }

    public function applicants(): array
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $jobsTable = new DatabaseTable('job', 'id');
            $job = $jobsTable->find('id', $_GET['id']);

            $stmt = 'SELECT * FROM applicants WHERE jobId = :id';
            $applicants = $jobsTable->custom($stmt, ['id' => $_GET['id']], false);

            $template = ($_SESSION['userDetails']['userType'] == 'admin') ? '../templates/admin/applicants.html.php' : '../templates/client/clientsApplicants.html.php';
            $title = ($_SESSION['userDetails']['userType'] == 'admin') ? 'Jo\'s Jobs - Applicants' : 'Jo\'s Jobs - Client Applicants';

        }

        return [
            'template' => $template,
            'variables' => ['job' => $job, 'applicants' => $applicants],
            'title' => $title
        ];
    }

    public function manageEnquiry(): array
    {
        adminValidation();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $contactTable = new DatabaseTable('contact', 'id');
            if ($_POST) {
                if (isset($_POST['responded']) && $_POST['responded'] == 'on') {
                    $updateResponse = ['id' => $_POST['id'], 'userId' => $_SESSION['userDetails']['userId']];
                } else {
                    $updateResponse = ['id' => $_POST['id'], 'userId' => NULL];
                }
                $contactTable->update($updateResponse);
            }

            $stmt = 'SELECT c.id, c.fullname, c.enquiry, c.email, c.phoneNumber, c.userId, u.userId as adminId
                 FROM contact c 
                 LEFT JOIN user u ON u.userId = c.userId';
            $criteria = [];

            $contacts = $contactTable->custom($stmt, $criteria, false);
        }

        return ['template' => '../templates/admin/manageEnquiry.html.php',
            'variables' => ['contacts' => $contacts],
            'title' => 'Jo\'s Jobs - Enquiries List'
        ];
    }

    public function clientIndex(): array
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            return ['template' => '../templates/client/clientIndex.html.php',
                'variables' => [],
                'title' => 'Jo\'s Jobs - Client Home'
            ];
        }

    }

    public function clientJobs(): array
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $jobsTable = new DatabaseTable('job', 'id');

            $categoryTable = new DatabaseTable('category', 'id');
            $category = $categoryTable->findAll();

            $stmt = 'SELECT j.id, j.title, j.description, j.salary, j.categoryId, j.archive, j.userId, (SELECT count(*) FROM applicants WHERE jobId = j.id) as count, c.id as catId, u.userId as userId, u.userType, c.name
                    FROM job j 
                    JOIN user u ON u.userId = j.userId
                    LEFT JOIN category c ON c.id = j.categoryId';

            $criteria = [];

            if ($_SESSION['userDetails']['userType'] == 'client') {
                $stmt .= ' WHERE j.userId = :userId';
                $criteria = [
                    'userId' => $_SESSION['userDetails']['userId']
                ];
            }


            if (isset($_GET['id'])) {
                if ($_SESSION['userDetails']['userType'] == 'client') {
                    $stmt .= ' AND j.categoryId = :id ';
                } else {
                    $stmt .= ' WHERE j.categoryId = :id ';
                }
                $criteria['id'] = $_GET['id'];
            }

            $jobs = $jobsTable->custom($stmt, $criteria, false);
        }

        return ['template' => '../templates/client/clientJobs.html.php',
            'variables' => ['jobs' => $jobs, 'category' => $category],
            'title' => 'Jo\'s Jobs - Client Home'
        ];
    }

    public function clientAddJob(): array
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $categoriesTable = new DatabaseTable('category', 'id');
            $categories = $categoriesTable->findAll();

            if (isset($_POST['submit'])) {
                $jobsTable = new DatabaseTable('job', 'id');
                $criteria = [
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'salary' => $_POST['salary'],
                    'location' => $_POST['location'],
                    'categoryId' => $_POST['categoryId'],
                    'closingDate' => $_POST['closingDate'],
                    'userId' => $_SESSION['userDetails']['userId']
                ];
                $jobs = $jobsTable->save($criteria);
                echo 'Job Added';
            }
        }

        return ['template' => '../templates/client/clientAddJob.html.php',
            'variables' => ['categories' => $categories],
            'title' => 'Jo\'s Jobs - Client Home'
        ];
    }

    public function editCategory(): array
    {
        adminValidation();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $categoriesTable = new DatabaseTable('category', 'id');
            $currentCategory = $categoriesTable->find('id', $_GET['id']);

            if (isset($_POST['submit'])) {
                $updateCategory = $categoriesTable->update(
                    [
                        'name' => $_POST['name'],
                        'id' => $_POST['id']
                    ]);
                echo 'Category Saved';
            }
        }

        return ['template' => '../templates/admin/editCategory.html.php',
            'variables' => ['currentCategory' => $currentCategory],
            'title' => 'Jo\'s Jobs - Client Home'
        ];
    }
}