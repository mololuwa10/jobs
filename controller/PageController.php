<?php

class PageController
{
    public function faqs(): array
    {
        return ['template' => '../templates/faqs.html.php',
            'variables' => [],
            'title' => 'Jo\'s Jobs - FAQs'
        ];
    }

    public function categories(): array
    {
        $categoriesTable = new databaseTable('category', 'id');
        $categories = $categoriesTable->findAll();

        $jobsTable = new databaseTable('job', 'id');
        $date = new DateTime();
        $criteria = [
            'date' => $date->format('Y-m-d'),
            'id' => $_GET['id']
        ];
        $jobs = $jobsTable->customFind('categoryId = :id AND closingDate > :date', $criteria);
        $currentCategory = $categoriesTable->find('id', $_GET['id']);

        return ['template' => '../templates/category.html.php',
            'variables' => ['jobs' => $jobs, 'categories' => $categories, 'currentCategory' => $currentCategory],
            'title' => 'Jo\'s Jobs - Categories'
        ];
    }

    public function home(): array
    {
        $jobsTable = new databaseTable('job', 'id');

        $locations = $jobsTable->custom('SELECT DISTINCT location FROM job', [], false);
        $criteria = [];

        $stmt = 'SELECT j.*, c.id as catId
        FROM job j
        LEFT JOIN category c ON c.id = j.categoryId
        WHERE (j.archive = 0 OR j.archive IS NULL)';

        if (isset($_GET["location"])) {
            $stmt .= ' AND j.location = :location';
            $criteria = ["location" => $_GET["location"]];
        }

        $stmt .= ' ORDER BY j.closingDate ASC LIMIT 10';
        $jobs = $jobsTable->custom($stmt, $criteria, false);

        return ['template' => '../templates/index.html.php',
            'variables' => ['jobs' => $jobs, 'locations' => $locations],
            'title' => 'Jo\'s Jobs - Home'
        ];
    }

    public function aboutUs(): array
    {
        $categoriesTable = new databaseTable('category', 'id');
        $categories = $categoriesTable->findAll();

        return ['template' => '../templates/aboutUs.html.php',
            'variables' => ['categories' => $categories],
            'title' => 'Jo\'s Jobs - About us'
        ];
    }

    public function contact(): array
    {
        $errorMessage = [];
        $categoriesTable = new databaseTable('category', 'id');
        $categories = $categoriesTable->findAll();

        if (isset($_POST['submit'])) {
            $fullName = $_POST['fullName'];
            $email = $_POST['email'];
            $phoneNumber = $_POST['phoneNumber'];
            $enquiry = $_POST['enquiry'];

            if (empty($fullName) || empty($email) || empty($phoneNumber) || empty($enquiry)) {
                $errorMessage[] = "EVERY FIELD IS NOT FIELD";
            }
            if (empty($errorMessage)) {
                $contactTable = new databaseTable('contact', 'id');
                $contact = $contactTable->find('email', $email);

                if ($contact) {
                    $errorMessageArray[] = "CREDENTIALS ALREADY EXIST";
                } else {
                    $criteria = [
                        'fullname' => $fullName,
                        'email' => $email,
                        'enquiry' => $enquiry,
                        'phoneNumber' => $phoneNumber
                    ];
                    $addEnquiry = $contactTable->insert($criteria);
                    echo 'ENQUIRY RECEIVED';
                }
            }
        }

        return ['template' => '../templates/contact.html.php',
            'variables' => ['categories' => $categories],
            'title' => 'Jo\'s Jobs - Contact Us'
        ];
    }

    public function apply(): array
    {
        $categoriesTable = new databaseTable('category', 'id');
        $categories = $categoriesTable->findAll();

        $jobTable = new databaseTable('job', 'id');
        $job = $jobTable->find('id', $_GET['id']);

        if (isset($_POST['submit'])) {
            if ($_FILES['cv']['error'] == 0) {
                $parts = explode('.', $_FILES['cv']['name']);
                $extension = end($parts);
                $fileName = uniqid() . '.' . $extension;
                move_uploaded_file($_FILES['cv']['tmp_name'], 'cvs/' . $fileName);

                $criteria = [
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],
                    'details' => $_POST['details'],
                    'jobId' => $_POST['jobId'],
                    'cv' => $fileName
                ];
                $applicantsTable = new databaseTable('applicants', 'id');
                $apply = $applicantsTable->save($criteria);

                echo 'Your application is complete. We will contact you after the closing date.';
            } else {
                echo 'There was an error uploading your CV';
            }
        }

        return ['template' => '../templates/apply.html.php',
            'variables' => ['job' => $job, 'categories' => $categories],
            'title' => 'Jo\'s Jobs - Apply'
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
}

?>