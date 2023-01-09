<main class="sidebar">
    <?php require 'admin-sidebar.html.php' ?>
    <section class="right">
        <h2>Enquiries</h2>
        <?php
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th style="width: 19%">S/N</th>';
        echo '<th class="category" style="width: 15%">Full Name</th>';
        echo '<th class="salary" style="width: 15%">E-mail</th>';
        echo '<th class="salary" style="width: 15%">Enquiry</th>';
        echo '<th class="salary" style="width: 15%">Phone Number</th>';
        echo '<th class="salary" style="width: 15%">Responded</th>';
        echo '<th style="width: 5%">&nbsp;</th>';
        echo '<th style="width: 15%">&nbsp;</th>';
        echo '<th style="width: 5%">&nbsp;</th>';
        echo '<th style="width: 5%">&nbsp;</th>';
        echo '</tr>';

        foreach ($contacts as $contact) {
            echo '<tr>';
            echo '<td style="text-align: center">' . $contact['id'] . '</td>';
            echo '<td style="text-align: center">' . $contact['fullname'] . '</td>';
            echo '<td style="text-align: center">' . $contact['email'] . '</td>';
            echo '<td style="text-align: center">' . $contact['enquiry'] . '</td>';
            echo '<td style="text-align: center">' . $contact['phoneNumber'] . '</td>';

            echo '<td><form method="post" action="manageEnquiry">';
            echo '<input type="hidden" name="id" value="' . $contact['id'] . '" />';
            echo '<div class="checkbox-container">';
            if ($contact['userId']) {
                echo '<input type="checkbox" checked name="responded" class="cb1" />';
            } else {
                echo '<input type="checkbox" name="responded" class="cb1" />';
            }
            echo '<label for="cb1"> Completed </label>';
            echo '</div>';
            echo '<button type="submit" class="completed-enquiry">Submit</button>';
            echo '</form></td>';
            echo '</tr>';
        }

        echo '</thead>';
        echo '</table>';
        ?>

    </section>
</main>

