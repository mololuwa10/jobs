<main class="sidebar">
    <?php require 'client-sidebar.html.php' ?>
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
            echo '<td>' . $applicant['name'] . '</td>';
            echo '<td>' . $applicant['email'] . '</td>';
            echo '<td>' . $applicant['details'] . '</td>';
            echo '<td><a href="/cvs/' . $applicant['cv'] . '">Download CV</a></td>';
            echo '</tr>';
        }

        echo '</thead>';
        echo '</table>';
        ?>

    </section>
</main>
