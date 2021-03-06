<?php
require_once "/etc/mysql_zugriff/zugriff.inc.php";
session_start();
echo '<link rel="stylesheet" type="text/css" href="style.css">';
if (isset($_SESSION['perms']) and ($_SESSION['perms'] & 1 and mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM entries WHERE id=" . mysqli_real_escape_string($db, $_GET["id"])))["author"] == $_SESSION["id"]) or $_SESSION["perms"] & 2) {
    if (!empty($_POST['title']) and !empty($_POST['content']) and !empty($_GET['id'])) {
        $title = mysqli_real_escape_string($db, $_POST['title']);
        $content = mysqli_real_escape_string($db, $_POST['content']);
        $id = mysqli_real_escape_string($db, $_GET['id']);
        $published = (($_SESSION['perms'] & 8 ? true : false) && isset($_POST["publish"]) ? 1 : 0) ? 1 : 0;
        mysqli_query($db, "UPDATE entries SET title='$title', content='$content', author=$_SESSION[id], published=$published WHERE id=$id;"); // TODO add column published
        header("Location: .");
    } else if (isset($_GET['id'])) {
        $id = mysqli_real_escape_string($db, $_GET['id']);
        $entries = mysqli_query($db, "SELECT * FROM entries WHERE id=$id");
        $entry = mysqli_fetch_assoc($entries);
        $title = $entry['title'];
        $content = $entry['content'];
        $checked = $entry["published"] ? "checked=\"checked\"" : "";
        echo <<<FORM
        <div id="content">
            <form action="edit.php?id=$_GET[id]" method="post">
            <input type="text" name="title" placeholder="Überschrift" value='$title'></input><a href=".">Home</a><br>
            <textarea id="editor" cols="65" rows="15" name="content">$content</textarea><br>
            <label for="publish">Publish</label>
            <input type="checkbox" id="publish" name="publish" value="true" $checked>
            <input type="submit" value="Posten" id="submit">
            </form>
        </div>
        <script src="../ckeditor/ckeditor.js"></script>
        <script>
            CKEDITOR.config.height='70%';
            CKEDITOR.replace('editor');
        </script>
    FORM;
    }
}
