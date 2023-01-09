<main class="sidebar">
    <?php
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
        ?>
        <section class="left">
            <ul>
                <li><a href="clientJobs">Jobs</a></li>
            </ul>
        </section>

        <section class="right">
            <h2>You are now logged in</h2>
        </section>
        <?php
    }
    ?>
</main>
