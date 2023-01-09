<main class="sidebar">
    <?php require 'admin-sidebar.html.php' ?>
    <h2>Add Admin/Client User</h2>
    <form action="addUser" method="post" style="padding: 40px">
        <label>Enter Full Name</label>
        <input type="text" name="fullName"/>

        <label>Enter Username</label>
        <input type="text" name="username"/>

        <label>Add User Type</label>

        <select name="userType">
            <option value="admin">Admin</option>
            <option value="client">Client</option>
        </select>

        <label>Enter Password</label>
        <input type="password" name="password"/>

        <input type="submit" name="submit" value="Add User"/>
    </form>
</main>
