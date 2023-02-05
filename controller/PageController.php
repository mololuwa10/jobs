<?php

namespace controller;

use Database\DatabaseTable;
use DateTime;

class PageController
{
    private $get;

    private $post;
    private $dbName;


    public function __construct(array $get, array $post, $dbName = 'job') {
        $this->get = $get;
        $this->post = $post;
        $this->dbName = $dbName;
    }
    public function faqs(): array
    {
        return ['template' => '../templates/layout/faqs.html.php',
            'variables' => [],
            'title' => 'Jo\'s Jobs - FAQs'
        ];
    }

    public function categories(): array
    {
        $categoriesTable = new DatabaseTable('category', 'id', $this->dbName);
        $categories = $categoriesTable->findAll();

        $jobsTable = new DatabaseTable('job', 'id', $this->dbName);
        $date = new DateTime();

        $criteria = [
            'date' => $date->format('Y-m-d'),
            'id' => $this->get['id']
        ];

        $jobs = $jobsTable->customFind('categoryId = :id AND closingDate > :date', $criteria);

        $currentCategory = $categoriesTable->find('id', $this->get['id']);

        return ['template' => '../templates/layout/category.html.php',
            'variables' => ['jobs' => $jobs, 'categories' => $categories, 'currentCategory' => $currentCategory],
            'title' => 'Jo\'s Jobs - Categories'
        ];
    }

    public function home(): array
    {
        $jobsTable = new DatabaseTable('job', 'id', $this->dbName);

        $locations = $jobsTable->custom('SELECT DISTINCT location FROM job', [], false);
        $criteria = [];
        $date = new DateTime();

        $stmt = 'SELECT j.*, c.id as catId
        FROM job j
        LEFT JOIN category c ON c.id = j.categoryId
        WHERE j.archive = 0 OR j.archive IS NULL AND j.closingDate > :date';

        $criteria["date"] = $date->format('Y-m-d');

        if (isset($this->get["location"]) && $this->get["location"] != 'All') {
            $stmt .= ' AND j.location = :location';
            $criteria["location"] = $this->get["location"];
        }

        $stmt .= ' ORDER BY j.closingDate ASC LIMIT 10';
        $jobs = $jobsTable->custom($stmt, $criteria, false);

        return [
            'template' => '../templates/layout/index.html.php',
            'variables' => [
                'jobs' => $jobs,
                'locations' => $locations
            ],
            'title' => 'Jo\'s Jobs - Home'
        ];
    }

    public function aboutUs(): array
    {
        $categoriesTable = new DatabaseTable('category', 'id', $this->dbName);
        $categories = $categoriesTable->findAll();

        return ['template' => '../templates/layout/aboutUs.html.php',
            'variables' => ['categories' => $categories],
            'title' => 'Jo\'s Jobs - About us'
        ];
    }

    public function contact(): array
    {
        $errorMessage = [];
        $validationMessage = '';
        $categoriesTable = new DatabaseTable('category', 'id', $this->dbName);
        $categories = $categoriesTable->findAll();

        if (isset($this->post['submit'])) {
            $fullName = $this->post['fullname'];
            $email = $this->post['email'];
            $phoneNumber = $this->post['phoneNumber'];
            $enquiry = $this->post['enquiry'];

            if (empty($fullName) || empty($email) || empty($phoneNumber) || empty($enquiry)) {
                $errorMessage[] = "EVERY FIELD IS NOT FIELD";
            }
            if (empty($errorMessage)) {
                $contactTable = new DatabaseTable('contact', 'id', $this->dbName);
                    $criteria = [
                        'fullname' => $fullName,
                        'email' => $email,
                        'enquiry' => $enquiry,
                        'phoneNumber' => $phoneNumber
                    ];
                    $addEnquiry = $contactTable->insert($criteria);

                    $validationMessage = 'ENQUIRY RECEIVED';
            }
        }

        return ['template' => '../templates/layout/contact.html.php',
            'variables' => ['categories' => $categories, 'errorMessage' => $errorMessage, 'validationMessage' => $validationMessage],
            'title' => 'Jo\'s Jobs - Contact Us'
        ];
    }

    public function apply(): array
    {
        $errorMessage = [];
        $categoriesTable = new DatabaseTable('category', 'id', $this->dbName);
        $categories = $categoriesTable->findAll();

        $jobTable = new DatabaseTable('job', 'id', $this->dbName);
        $job = $jobTable->find('id', $this->get['id']);

        if (isset($this->post['submit'])) {
            if ($_FILES['cv']['error'] == 0) {
                $parts = explode('.', $_FILES['cv']['name']);
                $extension = end($parts);
                $fileName = uniqid() . '.' . $extension;
                move_uploaded_file($_FILES['cv']['tmp_name'], 'cvs/' . $fileName);

                $criteria = [
                    'name' => $this->post['name'],
                    'email' => $this->post['email'],
                    'details' => $this->post['details'],
                    'jobId' => $this->post['jobId'],
                    'cv' => $fileName
                ];
                $applicantsTable = new DatabaseTable('applicants', 'id', $this->dbName);
                $apply = $applicantsTable->save($criteria);

                $errorMessage[] = 'Your application is complete. We will contact you after the closing date.';
            } else {
                $errorMessage[] = 'There was an error uploading your CV';
            }
        }

        return ['template' => '../templates/layout/apply.html.php',
            'variables' => ['job' => $job, 'categories' => $categories, 'errorMessage' => $errorMessage],
            'title' => 'Jo\'s Jobs - Apply'
        ];
    }

}
