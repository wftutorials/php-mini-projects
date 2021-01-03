<?php

function get_connection(){
    $dsn = "mysql:host=localhost;dbname=wftutorials";
    $user = "root";
    $passwd = "";
    $conn = new PDO($dsn, $user, $passwd);
    return $conn;
}

function save_snippet($title, $language, $url, $content, $id){
    $conn = get_connection();
    if($id){
        $sql = "UPDATE code_snippets SET `title`=?, `language`=?, `content`=?, `url`=? WHERE id=?";
        $query = $conn->prepare($sql);
        $query->execute([$title, $language, $content, $url, $id]);
        return $id;
    }else{
        $sql = "INSERT INTO code_snippets (`title`,`language`,`content`,`url`) VALUES (?,?,?,?)";
        $query = $conn->prepare($sql);
        $query->execute([$title, $language, $content, $url]);
        return $conn->lastInsertId();
    }
}

function get_all_snippets(){
    $results = [];
    try {
        $conn = get_connection();
        $results = $conn->query("SELECT * from code_snippets");
    }catch (Exception $e){

    }
    return $results;
}

function get_snippet($id){
    $results = [];
    try{
        $conn = get_connection();
        $query = $conn->prepare("SELECT * from code_snippets WHERE id=? LIMIT 1");
        $query->execute([$id]);
        $results = $query->fetchAll();
        if(isset($results[0])){
            $results = $results[0];
        }else{
            $results = [];
        }
    }catch (Exception $e){

    }
    return $results;
}

$id = "";
$content = "";
$title = "";
$language = "";
$url = "";

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $data = get_snippet($id);
    if($data){
        $content = $data["content"];
        $title = $data["title"];
        $url = $data["url"];
        $language = $data["language"];
    }
}



if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['save-code'])){

    $title = isset($_POST['title']) ? $_POST['title'] : null;
    $language = isset($_POST['language']) ? $_POST['language'] : null;
    $content = isset($_POST['content']) ? $_POST['content'] : null;
    $url = isset($_POST['url']) ? $_POST['url'] : null;
    if($title && $language && $content){
        $id = save_snippet($title, $language, $url, $content, $id);
    }

}

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['delete-code'])){
    if($id){
        $conn = get_connection();
        $query = $conn->prepare("DELETE FROM code_snippets WHERE id= ?");
        $query->execute([$id]);
        header("Location: app.php");
        exit();
    }

}


if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['delete-confirm'])){

    echo '
    <form method="post">
           Are you sure you want to delete this: <input type="submit" name="delete-code" value="yes"/>
           <button onclick="javascript:history(-1);">No</button>
    </form>
    ';
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Code Snippets</title>
    <script
            src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
            crossorigin="anonymous"></script>
    <link rel="stylesheet"
          href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/styles/atom-one-dark.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/highlight.min.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>
</head>
<body>
<style>
.container{
    width: 98%;
    background: #ddd;
    float: left;
    padding: 20px;
}
.form-container {
    width: 40%;
    display: inline-block;
}

.form-content{
    background: #f6f6f6;
    border-radius: 3px;
    border: 1px solid #ccc;
    padding: 20px;
}

.snippet-listing {
    background: #f6f6f6;
    border-radius: 3px;
    border: 1px solid #ccc;
    padding: 20px;
    min-height: 300px;
    max-height: 500px;
}

.snippet-listing-content {
    max-height: 200px; overflow: auto;
}

.view-container {
    width: 50%;
    float: right;
    background: #f6f6f6;
    border-radius: 3px;
    border: 1px solid #ccc;
    padding: 20px;
    min-height: 500px;
    margin-right: 3px;
}

.code-block code {
    border-radius: 3px;
    border: 4px solid #ccc;
}

</style>

<div class="container">

    <!-- Left Side -->
    <div class="form-container">

        <div class="form-content">
            <h3>Code Snippets</h3>
            <a href="app.php">Add New Snippet</a>
            <br><br>
            <form  method="post">
                Title: <input value="<?php echo $title;?>" placeholder="Enter snippet title" type="text" name="title" autocomplete="off"/><br>
                Languages :
                <select name="language">
                    <option>--Select a language</option>
                    <option value="php" <?php if($language=="php"){ echo 'selected="selected"';}?>>PHP</option>
                    <option value="javascript" <?php if($language=="javascript"){ echo 'selected="selected"';}?>>JavaScript</option>
                    <option value="java" <?php if($language=="java"){ echo 'selected="selected"';}?>>JAVA</option>
                    <option value="sql" <?php if($language=="sql"){ echo 'selected="selected"';}?>>SQL</option>
                    <option value="bash" <?php if($language=="bash"){ echo 'selected="selected"';}?>>BASH</option>
                    <option value="python" <?php if($language=="python"){ echo 'selected="selected"';}?>>PYTHON</option>
                </select>
                <br>
                Web Link : <input value="<?php echo $url;?>" placeholder="What is the link" type="text" name="url" autocomplete="off"/> <br>
                Code: <br>
                <textarea name="content" cols="50" rows="7"><?php echo $content;?></textarea><br>
                <?php if($id):?>
                <hr>
                        Do you want to delete this snippet? :<button type="submit" name="delete-confirm">Yes.delete</button>
                <hr>
                <?php endif;?>
                <button type="submit" name="save-code">Save Code Snippet</button>


            </form>
        </div>

        <br>

        <div class="snippet-listing">
            <h3>All Snippets</h3>
                <div class="snippet-listing-content">
                    <ul>
                        <?php foreach (get_all_snippets() as $snippet):?>
                            <li><a href="app.php?id=<?php echo $snippet["id"];?>"><?Php echo $snippet["title"];?></a></li>
                        <?php endforeach;?>
                    </ul>
                </div>
        </div>

    </div>
    <!-- end of left side -->

    <!-- Start of right side-->

    <div class="view-container">
        <h3>View Code Snippet</h3>
        <?php if($title):?>
        <p><?php echo $title;?></p>
        <?php endif;?>
        <?php if($url):?>
            <p><a target="_blank" href="<?php echo $url;?>"><?php echo $url;?></a></p>
        <?php endif;?>
        <div class="code-block">
            <pre>
                <code class="lanugage-<?php echo $language;?>"><?php echo $content;?></code>
            </pre>
        </div>
    </div>

</div>


</body>
</html>
