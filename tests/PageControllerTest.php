<?php

use Database\DatabaseTable;

class PageControllerTest extends \PHPUnit\Framework\TestCase
{
    private $categoriesTable;
    private $jobsTable;
    private $date;

    public function testFindAllCategories()
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

    protected function setUp(): void
    {
        $this->pdo = new \PDO('mysql:dbname=testJob;host=mysql', 'student', 'student');
        $this->categoriesTable = new DatabaseTable('testCategory', 'id');
        $this->jobsTable = new DatabaseTable('testJob', 'id');
        $this->contactTable = new DatabaseTable('testContact', 'id');
        $this->applicantTable = new DatabaseTable('testApplicants', 'id');
        $this->date = new DateTime();
    }
}
