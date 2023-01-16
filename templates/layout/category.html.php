<main class="sidebar">
    <section class="left">
        <ul>
            <?php
            $categories = $categories ?? [];
            foreach ($categories as $c) {
                echo '<li><a href="categories?id=' . $c['id'] . '">' . $c['name'] . '</a></li>';
            }
            ?>
        </ul>
    </section>
    <section class="right">
        <h1><?php
            if (isset($currentCategory)) {
                echo $currentCategory['name'];
            } ?> Jobs</h1>

        <ul class="listing">
            <?php
            $jobs = $jobs ?? [];
            foreach ($jobs as $job) {
                if ($job['archive'] == 0) {
                    echo '<li>';
                    echo '<div class="details">';
                    echo '<h2>' . $job['title'] . '</h2>';
                    echo '<h3>' . $job['salary'] . '</h3>';
                    echo '<p>' . nl2br($job['description']) . '</p>';

                    echo '<a class="more" href="../apply?id=' . $job['id'] . '">Apply for this job</a>';

                    echo '</div>';
                    echo '</li>';
                } else {
                    echo '';
                }
            }
            ?>
        </ul>
    </section>
</main>