<main class="home">
    <p>Welcome to Jo's Jobs, we're a recruitment agency based in Northampton. We offer a range of different office
        jobs. Get in touch if you'd like to list a job with us.</p>

    <div class="location-filter">
        <form action="home" class="job-filter" method="get">
            <select name="location">
                <?php
                foreach ($locations as $location) {
                    echo '<option value=' . $location['location'] . '>' . 'Filter By Location: ' . $location['location'] . '</option>';
                }
                ?>
            </select>
            <input type="submit" name="submit" value="Search"/>
        </form>
    </div>

    <h2>Select the type of job you are looking for:</h2>

    <ul class="job-listing">
        <?php
        foreach ($jobs as $job) {
            echo '<li>';
            echo '<div class="details">';
            echo '<h2>' . $job['title'] . '</h2>';
            echo '<h3>' . $job['salary'] . '</h3>';
            echo '<h3> Closing Date: ' . $job['closingDate'] . '</h3>';
            echo '<p>' . nl2br($job['description']) . '</p>';

            echo '<a class="more" href="../apply?id=' . $job['id'] . '">Apply for this job</a>';

            echo '</div>';
            echo '</li>';
        }
        ?>
    </ul>
</main>