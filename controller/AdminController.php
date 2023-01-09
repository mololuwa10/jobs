<?php

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

            $userAdminTable = new databaseTable('user', 'userId');
            $user = $userAdminTable->find('userName', $userName);

            if ($user) {
                $_SESSION['loggedin'] = $user['userId'];
                $_SESSION['userDetails'] = $user;
                if (password_verify($password, $user['password']) && $user['userType'] == 'client') {
                    header("Location: clientIndex");
                } else if ($user['userType'] == 'admin') {
                    header("Location: adminIndex");
                }
            } else {
                echo 'CREDENTIALS ARE WRONG!! TRY AGAIN';
                $errorFlag = true;
            }
        }

        return ['template' => '../templates/adminLogin.html.php',
            'variables' => [],
            'title' => 'Jo\'s Jobs - Admin Login'
        ];
    }

    public function adminIndex(): array
    {
        adminValidation();
        return ['template' => '../templates/adminIndex.html.php',
            'variables' => [],
            'title' => 'Jo\'s Jobs - Admin Home'
        ];
    }

    public function jobs(): array
    {
        adminValidation();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $jobsTable = new databaseTable('job', 'id');

            $categoryTable = new databaseTable('category', 'id');
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
        return ['template' => '../templates/jobs.html.php',
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
                    $userAdminTable = new databaseTable('user', 'userId');
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
        return ['template' => '../templates/addUser.html.php',
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
        header("Location: ../home");
    }

    public function addJob(): array
    {
        adminValidation();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $categoriesTable = new databaseTable('category', 'id');
            $categories = $categoriesTable->findAll();

            if (isset($_POST['submit'])) {
                $jobsTable = new databaseTable('job', 'id');
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

        return ['template' => '../templates/addjob.html.php',
            'variables' => ['categories' => $categories],
            'title' => 'Jo\'s Jobs - Add Job'
        ];
    }

    public function editJob(): array
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $jobsTable = new databaseTable('job', 'id');
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

            $categoriesTable = new databaseTable('category', 'id');
            $stmt = $categoriesTable->findAll();
        }

        return ['template' => '../templates/editJob.html.php',
            'variables' => ['job' => $job, 'stmt' => $stmt],
            'title' => 'Jo\'s Jobs - Edit Job'
        ];
    }

    public function addCategory(): array
    {
        adminValidation();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            if (isset($_POST['submit'])) {
                $categoriesTable = new databaseTable('category', 'id');
                $criteria = [
                    'name' => $_POST['name']
                ];
                $category = $categoriesTable->save($criteria);

                echo 'Category added';
            }
        }

        return ['template' => '../templates/addcategory.html.php',
            'variables' => [],
            'title' => 'Jo\'s Jobs - Add Category'
        ];
    }

    public function archiveJob(): void
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $jobsTable = new databaseTable('job', 'id');
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
            $categoryTable = new databaseTable('category', 'id');
            $categories = $categoryTable->findAll();
        }

        return ['template' => '../templates/adminCategories.html.php',
            'variables' => ['categories' => $categories],
            'title' => 'Jo\'s Jobs - Category'
        ];
    }

    public function deleteCategories(): void
    {
        adminValidation();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            $categoryTable = new databaseTable('category', 'id');
            $deleteCategory = $categoryTable->custom('DELETE FROM category WHERE id = :id', ['id' => $_POST['id']], true);

            header('location: categories');
        }
    }

    public function applicants(): array
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $jobsTable = new databaseTable('job', 'id');
            $job = $jobsTable->find('id', $_GET['id']);

            $stmt = 'SELECT * FROM applicants WHERE jobId = :id';
            $applicants = $jobsTable->custom($stmt, ['id' => $_GET['id']], false);

            $template = ($_SESSION['userDetails']['userType'] == 'admin') ? '../templates/applicants.html.php' : '../templates/clientsApplicants.html.php';
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
            $contactTable = new databaseTable('contact', 'id');
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

        return ['template' => '../templates/manageEnquiry.html.php',
            'variables' => ['contacts' => $contacts],
            'title' => 'Jo\'s Jobs - Enquiries List'
        ];
    }

    public function clientIndex(): array
    {
        return ['template' => '../templates/clientIndex.html.php',
            'variables' => [],
            'title' => 'Jo\'s Jobs - Client Home'
        ];
    }

    public function clientJobs(): array
    {
        $jobsTable = new databaseTable('job', 'id');

        $categoryTable = new databaseTable('category', 'id');
        $category = $categoryTable->findAll();

        $stmt = 'SELECT j.id, j.title, j.description, j.salary, j.categoryId, j.archive, j.userId, (SELECT count(*) FROM applicants WHERE jobId = j.id) as count, c.id as catId, u.userId as userId, u.userType, c.name
        FROM job j 
        JOIN user u ON u.userId = j.userId
        LEFT JOIN category c ON c.id = j.categoryId
        ';

        $criteria = [];

        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            if ($_SESSION['userDetails']['userType'] == 'client') {
                $stmt .= ' WHERE j.userId = :userId';
                $criteria = [
                    'userId' => $_SESSION['userDetails']['userId']
                ];
            }
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

        return ['template' => '../templates/clientJobs.html.php',
            'variables' => ['jobs' => $jobs, 'category' => $category],
            'title' => 'Jo\'s Jobs - Client Home'
        ];
    }

    public function clientAddJob(): array
    {
        $categoriesTable = new databaseTable('category', 'id');
        $categories = $categoriesTable->findAll();

        if (isset($_POST['submit'])) {
            $jobsTable = new databaseTable('job', 'id');
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

        return ['template' => '../templates/clientAddJob.html.php',
            'variables' => ['categories' => $categories],
            'title' => 'Jo\'s Jobs - Client Home'
        ];
    }

    public function editCategory(): array
    {
        adminValidation();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $categoriesTable = new databaseTable('category', 'id');
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

        return ['template' => '../templates/editCategory.html.php',
            'variables' => ['currentCategory' => $currentCategory],
            'title' => 'Jo\'s Jobs - Client Home'
        ];
    }
}