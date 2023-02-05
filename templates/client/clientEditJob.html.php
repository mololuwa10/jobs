<main class="sidebar">
    <?php require 'client-sidebar.html.php';
    $job = $job ?? [];
    ?>
    <section class="right">
        <h2>Edit Job</h2>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?php echo $job['id']; ?>"/>

            <label>Title</label>
            <input type="text" name="title" value="<?php echo $job['title']; ?>"/>

            <label>Description</label>
            <textarea name="description"><?php echo $job['description']; ?></textarea>

            <label>Location</label>
            <input type="text" name="location" value="<?php echo $job['location']; ?>"/>


            <label>Salary</label>
            <input type="text" name="salary" value="<?php echo $job['salary']; ?>"/>

            <label>Category</label>

            <select name="categoryId">
                <?php
                foreach ($stmt as $row) {
                    if ($job['categoryId'] == $row['id']) {
                        echo '<option selected="selected" value="' . $row['id'] . '">' . $row['name'] . '</option>';
                    } else {
                        echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                    }
                }
                ?>
            </select>
            <label>Closing Date</label>
            <input type="date" name="closingDate" value="<?php echo $job['closingDate']; ?>"/>

            <input type="submit" name="submit" value="Save"/>

        </form>
    </section>
</main>
