<?php
//require '../functions/loadTemplate.php';
//session_start();
//$jobTable = new databaseTable( 'job', 'id');
//$job = $jobTable->find('id', $_GET['id']);

//if (isset($_POST['submit'])) {
//    if ($_FILES['cv']['error'] == 0) {
//        $parts = explode('.', $_FILES['cv']['name']);
//        $extension = end($parts);
//        $fileName = uniqid() . '.' . $extension;
//        move_uploaded_file($_FILES['cv']['tmp_name'], 'cvs/' . $fileName);
//
//        $criteria = [
//            'name' => $_POST['name'],
//            'email' => $_POST['email'],
//            'details' => $_POST['details'],
//            'jobId' => $_POST['jobId'],
//            'cv' => $fileName
//        ];
//        $applicantsTable = new databaseTable('applicants', 'id');
//        $apply = $applicantsTable->save($criteria);
//
//        echo 'Your application is complete. We will contact you after the closing date.';
//    } else {
//        echo 'There was an error uploading your CV';
//    }
//}
//
//$title = 'Jo\'s Jobs - Apply';
//$output = loadTemplate('../templates/apply.html.php', ['job' => $job]);

//require '../templates/layout.html.php';