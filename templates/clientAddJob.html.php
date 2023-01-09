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

        <h2>Add Job</h2>

        <form action="clientAddJob" method="POST"
        ">
        <label>Title</label>
        <input type=" text" name="title"/>

        <label>Description</label>
        <textarea name="description"></textarea>

        <label>Salary</label>
        <input type="text" name="salary"/>

        <label>Location</label>
        <input type="text" name="location"/>

        <label>Category</label>

        <select name="categoryId">
            <?php
            foreach ($categories as $category) {
                echo '<option value="' . $category['id'] . '">' . $category['name'] . '</option>';
            }
            ?>
        </select>

        <label>Closing Date</label>
        <input type="date" name="closingDate"/>
        <input type="submit" name="submit" value="Add"/>

        </form>


        <?php
        }
        ?>
    </section>
</main>
