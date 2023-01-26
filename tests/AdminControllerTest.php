<?php

use Database\DatabaseTable;

class AdminControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testAddCategory(){
        // validate a user - admin

        $postData = [
            'submit' => 'submit',
            'name' => 'Test First Category'
        ];

        $adminController = new \controller\AdminController([], $postData, 'testJob');
        $response = $adminController->addCategory();

        // assert
//        $this->assertTrue($response);
        $this->assertEquals($response['variables']['message'], 'Category added');
    }

//    public function setUp() {
//
//    }
}