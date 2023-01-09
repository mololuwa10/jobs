<main class="sidebar">
    <?php require 'admin-sidebar.html.php' ?>
    <section class="right">
        <h2>Edit Category</h2>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?php echo $currentCategory['id']; ?>"/>
            <label>Name</label>
            <input type="text" name="name" value="<?php echo $currentCategory['name']; ?>"/>

            <input type="submit" name="submit" value="Save Category"/>
        </form>
    </section>
</main>
