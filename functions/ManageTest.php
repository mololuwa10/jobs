<?php
namespace functions;
use Database\DatabaseTable;
class ManageTest
{
    private $dbName;
    public function truncateTable(): void
    {
        $tableName = ['user', 'category', 'contact', 'job', 'applicants'];

        foreach ($tableName as $table) {
            $tables = new DatabaseTable($table, 'id', $this->dbName);
            $tables->custom('TRUNCATE TABLE ' . $table);
        }
    }

    public function addJob($jobData = []) {
        $jobsTable = new DatabaseTable('job', 'id', 'testJob');
        $criteria = [
            'title' => $jobData['title'] ?? 'Teaching Assistant',
            'description' => $jobData['description'] ?? 'You will be assisting a teacher',
            'salary' => $jobData['salary'] ?? '£30,000 - £40,000',
            'closingDate' => $jobData['closingDate'] ?? '2023-01-08',
            'categoryId' => $jobData['categoryId'] ?? 1,
            'location' => $jobData['location'] ?? 'Birmingham'
        ];
        $jobs = $jobsTable->insert($criteria);
    }

    public function register($data = []): void
    {
        $errMsgArray = [];
        $errorFlag = false;

        $criteria = [
            'fullName' => $data['fullName'],
            'userName' => $data['userName'],
            'password' => $data['password'],
            'userType' => $data['userType'],
        ];

        $hash_password = password_hash($criteria['password'], PASSWORD_DEFAULT);

        $userTestTable = new DatabaseTable('user', 'userId', 'testJob');
        $user = $userTestTable->insert($criteria);

        $_SESSION['loggedin'] = $user['userId'];
        $_SESSION['userDetails'] = $user;
    }
}