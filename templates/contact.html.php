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
        <h1>Contact Us</h1>
        <ul class="listing">
            <form action="contact" method="post" style="padding: 40px">
                <label>Full Name: </label>
                <input type="text" name="fullName"/>

                <label>Email Address: </label>
                <input type="email" name="email"/>

                <label>Phone Number: </label>
                <input type="text" name="phoneNumber"/>

                <label>Enquiry: </label>
                <textarea name="enquiry"></textarea>

                <input type="submit" name="submit" value="Submit Enquiry"/>
            </form>
        </ul>
    </section>
</main>