<main class="sidebar">
    <?php require 'client-sidebar.html.php' ?>
    <section class="right">

        <h2>Jobs</h2>

        <a class="new" href="addJob">Add new job</a>
        <div>
            <form action="clientJobs" class="job-filter" method="get">
                <select name="id">
                    <option disabled selected>Filter By Category: </option>
                    <option value="All">All Categories</option>
                    <?php
                    $category = $category ?? [];
                    foreach ($category as $row) {
                        echo '<option value =' . $row['id'] . '>' . 'Filter By Category: ' . $row['name'] . '</option>';
                    }
                    ?>
                </select>
                <input type="submit" name="submit" value="Search"/>
            </form>
        </div>
        <?php
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th style="width: 19%">Title</th>';
        echo '<th class="category" style="width: 15%">Category</th>';
        echo '<th class="salary" style="width: 15%">Salary</th>';
        echo '<th style="width: 5%">&nbsp;</th>';
        echo '<th style="width: 15%">&nbsp;</th>';
        echo '<th style="width: 5%">&nbsp;</th>';
        echo '<th style="width: 5%">&nbsp;</th>';
        echo '</tr>';

        foreach ($jobs as $job) {
            echo '<tr>';
            echo '<td style="text-align: center">' . $job['title'] . '</td>';
            echo '<td style="text-align: center">' . $job['name'] . '</td>';
            echo '<td>' . $job['salary'] . '</td>';
            echo '<td><a style="float: right" href="editjob?id=' . $job['id'] . '">Edit</a></td>';
            echo '<td><a style="float: right" href="applicants?id=' . $job['id'] . '">View applicants (' . $job['count'] . ')</a></td>';
            echo '<td><form method="post" action="archiveJob">';
            echo '<input type="hidden" name="id" value="' . $job['id'] . '" />';

            if ($job['archive'] == 1) {
                echo '<input type="submit" name="submit" value="Un-Archive Job" />';
                echo '<input type="hidden" name="archive" value="0" />';
            } else {
                echo '<input type="submit" name="submit" value="Archive Job" />';
                echo '<input type="hidden" name="archive" value="1" />';
            }
            echo '</form></td>';
            echo '</tr>';
        }

        echo '</thead>';
        echo '</table>';
        ?>

    </section>
</main>

