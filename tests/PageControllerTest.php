<?php

use PHPUnit\Framework\TestCase;

class PageControllerTest extends TestCase
{
    public function setUp()
    {
        $this->pdo = new PDO('mysql:dbname=testJob;host=mysql', 'student', 'student');
        $this->categoriesTable = new DatabaseTable('testCategory', 'id');
        $this->jobsTable = new DatabaseTable('testJob', 'id');
        $this->contactTable = new DatabaseTable('testContact', 'id');
        $this->applicantTable = new DatabaseTable('testApplicants', 'id');
        $this->date = new DateTime();
    }

    public function testFindAllCategories()
    {
        $categories = $this->categoriesTable->findAll();
        $this->assertTrue(is_array($categories));
    }
}
