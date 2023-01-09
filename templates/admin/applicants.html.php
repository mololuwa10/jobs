<main class="sidebar">
    <section class="left">
        <ul>
            <li><a href="jobs">Jobs</a></li>
            <li><a href="categories">Categories</a></li>
            <li><a href="addUser">Add User</a></li>
            <li><a href="manageEnquiry">Manage Enquiries</a></li>
        </ul>
    </section>
    <section class="right">
        <h2>Applicants for <?= $job['title']; ?></h2>
        <?php
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th style="width: 10%">Name</th>';
        echo '<th style="width: 10%">Email</th>';
        echo '<th style="width: 65%">Details</th>';
        echo '<th style="width: 15%">CV</th>';
        echo '</tr>';

        foreach ($applicants as $applicant) {
            echo '<tr>';
            echo '<td style="text-align: center">' . $applicant['name'] . '</td>';
            echo '<td style="text-align: center">' . $applicant['email'] . '</td>';
            echo '<td style="text-align: center">' . $applicant['details'] . '</td>';
            echo '<td><a href="/cvs/' . $applicant['cv'] . '">Download CV</a></td>';
            echo '</tr>';
        }

        echo '</thead>';
        echo '</table>';
        ?>

    </section>
</main>