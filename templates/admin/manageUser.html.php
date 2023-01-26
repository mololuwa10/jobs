<main class="sidebar">
    <?php require 'admin-sidebar.html.php' ?>
    <section class="right">
        <h2>Manage Users</h2>
        <?php
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th style="width: 20%">S/N</th>';
        echo '<th class="category" style="width: 20%">Full Name</th>';
        echo '<th class="category" style="width: 20%">Username</th>';
        echo '<th class="category" style="width: 20%">User Type</th>';
        echo '<th class="category" style="width: 20%">Delete</th>';
//        echo '<th style="width: 5%">&nbsp;</th>';
        echo '<th style="width: 15%">&nbsp;</th>';
        echo '<th style="width: 5%">&nbsp;</th>';
//        echo '<th style="width: 5%">&nbsp;</th>';
        echo '</tr>';

        foreach ($users as $user) {
            echo '<tr>';
            echo '<td style="text-align: center">' . $user['userId'] . '</td>';
            echo '<td style="text-align: center">' . $user['fullName'] . '</td>';
            echo '<td style="text-align: center">' . $user['userName'] . '</td>';
            echo '<td style="text-align: center">' . $user['userType'] . '</td>';

            echo '<td><form method="post" action="deleteUser">
				<input type="hidden" name="id" value="' . $user['userId'] . '" />
				<input type="submit" name="submit" value="Delete" class="completed-enquiry" style="margin-left: 10px"/>
				</form></td>';
            echo '</tr>';
        }

        echo '</thead>';
        echo '</table>';
        ?>
    </section>
</main>


