<main class="sidebar">
    <?php
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
        ?>
        <section class="left">
            <ul>
                <li><a href="jobs">Jobs</a></li>
                <li><a href="categories">Categories</a></li>
                <li><a href="addUser">Add User</a></li>
                <li><a href="manageEnquiry">Manage Enquiries</a></li>
            </ul>
        </section>

        <section class="right">
            <h2>You are now logged in</h2>
        </section>
        <?php
    } else {
        ?>
        <h2>Log in</h2>
        <form action="adminIndex" method="post" style="padding: 40px">
            <label>Enter Username</label>
            <input type="text" name="username"/>

            <label>Enter Password</label>
            <input type="password" name="password"/>

            <input type="submit" name="submit" value="Log In"/>
        </form>
        <?php
    }
    ?>
</main>