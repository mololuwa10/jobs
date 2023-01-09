<main class="sidebar">
    <?php require '../templates/admin-sidebar.html.php' ?>
    <section class="right">
        <h2>Categories</h2>
        <a class="new" href="addCategory">Add new category</a>
        <?php
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Name</th>';
        echo '<th style="width: 5%">&nbsp;</th>';
        echo '<th style="width: 5%">&nbsp;</th>';
        echo '</tr>';

        foreach ($categories as $category) {
            echo '<tr>';
            echo '<td>' . $category['name'] . '</td>';
            echo '<td><a style="float: right" href="editCategory?id=' . $category['id'] . '">Edit</a></td>';
            echo '<td><form method="post" action="deleteCategories">
				<input type="hidden" name="id" value="' . $category['id'] . '" />
				<input type="submit" name="submit" value="Delete" />
				</form></td>';
            echo '</tr>';
        }
        echo '</thead>';
        echo '</table>';
        ?>
    </section>
</main>
