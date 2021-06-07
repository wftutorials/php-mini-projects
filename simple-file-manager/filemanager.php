<?php
include "DBModel.php";
$currentFolder = null;

define("WEBSITE_BASE_PATH","http://localhost:63342/samples/");


function get_random_name($num = 6){
  $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
  $string = '';
  $max = strlen($characters) - 1;
  for ($i = 0; $i < $num; $i++) {
    $string .= $characters[mt_rand(0, $max)];
  }
  return $string;
}

function reload_to_page($page){
  header("Location: $page");
  exit();
}

function reload_current_directory(){
  reload_to_page(get_current_url());
}

function get_current_url(){
  if(in_folder()){
    return create_url("filemanager.php",['id'=>get_folder_id()]);
  }else{
    return create_url('filemanager.php');
  }
}

function create_url($path, $arguments=[]){
  $url = WEBSITE_BASE_PATH;
  if($path != "/"){
    $url .= $path;
  }
  if(count($arguments) > 0){
    $url .= "?";
    foreach ($arguments as $key=> $value){
        $url .= $key . "=" . $value;
    }
  }
  return $url;
}



function save_to_file_manager($filename, $realfilename, $type, $size, $folder){
  $model = new DBModel("file_manager");
  $model->insertRecord([
    'file_name' => $filename,
    'real_name'=> $realfilename,
    'file_size' => $size,
    'file_type' => $type,
    'folder'=> $folder
  ]);
}

function save_a_folder($name, $parent){
  $model = new DBModel("file_manager_folder");
  $model->insertRecord([
    'name' => $name,
    'parent' => $parent
  ]);
}


function get_all_files(){
  $model = new DBModel("file_manager");
  if(in_folder()){
    return $model->getAllBySQL("SELECT * from file_manager Where folder=?",[get_folder_id()]);
  }else{
    return $model->getAllBySQL("SELECT * from file_manager Where folder=0 OR folder IS NULL");
  }
}

function get_all_parent_folders(){
  $model = new DBModel("file_manager");
  if(in_folder()){
    return $model->getAllBySQL("SELECT * from file_manager_folder WHERE parent=?",[get_folder_id()]);
  }else{
    return $model->getAllBySQL("SELECT * from file_manager_folder WHERE parent IS NULL");
  }
}

function create_file_link($name){
  return WEBSITE_BASE_PATH . "uploads/". $name;
}

function in_folder(){
  if(get_folder_id()){
    return true;
  }
  return false;
}

function get_folder_id(){
  if(isset($_GET['id']) && !empty($_GET['id'])){
    return $_GET['id'];
  }
  return null;
}

function getCurrentFolder($id){
  global $currentFolder;
  if(isset($currentFolder["id"]) && $currentFolder["id"] == $id){
    return $currentFolder;
  }else{
    $model = new DBModel("file_manager_folder");
    $currentFolder = $model->getOneRecordByPk($id);
    return $currentFolder;
  }
}

function get_current_folder_name(){
  if(in_folder()){
    return "(" . getCurrentFolder(get_folder_id())["name"] . ")";
  }
}

function parent_up_url(){
  $folder = getCurrentFolder(get_folder_id());
  if($folder["parent"]){
    return create_url("filemanager.php",['id'=>$folder["parent"]]);
  }else{
    return create_url("filemanager.php");
  }
}

function get_icon($type, $name=""){
  if (strpos($type, 'excel') !== false) {
    return "<img src='ic_excel.png' style='width: 20px'/>";
  }else if(strpos($type, 'audio') !== false){
    return "<img src='ic_music.png' style='width: 20px'/>";
  }else if(strpos($type, 'pdf') !== false){
    return "<img src='ic_pdf.png' style='width: 20px'/>";
  }else if(strpos($type, 'openxmlformats') !== false){
    if(strpos($name, 'doc') !== false){
      return "<img src='ic_word.png' style='width: 20px'/>";
    }else if(strpos($name, 'ppt') !== false){
      return "<img src='ic_power_point.png' style='width: 20px'/>";
    }else if(strpos($name, 'xlsx') !== false){
      return "<img src='ic_excel.png' style='width: 20px'/>";
    }else{
      return "<img src='ic_file.png' style='width: 20px'/>";
    }
  }else if(strpos($type, 'image') !== false){
    return "<img src='ic_picture.png' style='width: 20px'/>";
  }else if(strpos($type, 'video') !== false){
    return "<img src='ic_video.png' style='width: 20px'/>";
  }else{
    return "<img src='ic_file.png' style='width: 20px'/>";
  }
}


if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])){
  $folder = isset($_POST['folder']) ? $_POST['folder'] : null;

  $uploadDir = "./uploads/";

  // file upload section
  if(isset($_FILES["file"]) && $_FILES["file"]["error"] == 0){
    $filename = $_FILES["file"]["name"];
    $filetype = $_FILES["file"]["type"];
    $filesize = $_FILES["file"]["size"];
    $random_name = get_random_name()  ."." . pathinfo($filename, PATHINFO_EXTENSION);
    if(file_exists($uploadDir . $random_name)){
      echo $filename . " is already exists.";
    } else{
      move_uploaded_file($_FILES["file"]["tmp_name"], $uploadDir . $random_name);
      //save_to_gallery($random_name, $filename);
      save_to_file_manager($random_name, $filename, $filetype, $filesize, get_folder_id());
      reload_current_directory();
    }
  }else{
    if($folder){
      save_a_folder($folder, get_folder_id());
      reload_current_directory();
    }
  }

}


// renaming the file

if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['file'])){

  if(is_numeric($_GET['file']) && isset($_GET['name'])){
    $model = new DBModel("file_manager");
    $record = $model->getOneRecordByPk($_GET['file']);
    $original =  $record["real_name"];
    $orig_extension = pathinfo($original, PATHINFO_EXTENSION);
    $model->updateRecordByPk($_GET['file'],[
      'real_name' => $_GET["name"] . "." . $orig_extension
    ]);
    reload_current_directory();
  }
}

// renaming the folder

if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['folder'])){
  if(is_numeric($_GET['folder']) && isset($_GET['name'])){
    $model = new DBModel("file_manager_folder");
    $record = $model->getOneRecordByPk($_GET['folder']);
    $original =  $record["name"];
    $model->updateRecordByPk($_GET['folder'],[
      'name' => $_GET["name"]
    ]);
    reload_current_directory();
  }
}

if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'delete'){

  $id = isset($_GET['item']) ? $_GET['item'] : null;
  if(isset($_GET['type']) && $_GET['type'] == 'file'){
    $model = new DBModel("file_manager");
    $model->deleteRecordByPk($id);
  }else if(isset($_GET['type']) && $_GET['type'] == 'folder'){
    $model = new DBModel("file_manager_folder");
    $model->deleteRecordByPk($id);
  }
  reload_current_directory();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>File Manager</title>
  <link rel="stylesheet" type = "text/css" href="./assets/jquery.contextMenu.css">
  <link rel="stylesheet" type = "text/css" href="./assets/jquery-confirm.min.css">
  <script
    src="https://code.jquery.com/jquery-3.5.1.min.js"
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
    crossorigin="anonymous"></script>
  <script src="./assets/jquery.ui.position.min.js"></script>
  <script src="./assets/jquery.contextMenu.min.js"></script>
  <script src="./assets/jquery-confirm.min.js"></script>
</head>
<style>
  table {
    width: 100%;
  }
  table, th, td {

  }
</style>
<body>
<h1>File Manager</h1>
<p>Add a file</p>
<form method="post" enctype="multipart/form-data">
  File: <input type="file" name="file"/>&nbsp;&nbsp;
  Folder: <input type="text" name="folder">&nbsp;&nbsp;
  <button type="submit" name="submit">Submit</button>
</form>

<br><br>
<h2><a href="<?Php echo create_url("filemanager.php");?>">Files and Folders</a> <?php echo get_current_folder_name();?></h2>

<table>
  <thead>
    <tr>
      <td>Name</td>
      <td>Date</td>
      <td>Type</td>
      <td>Size</td>
    </tr>
  </thead>
  <tbody>
      <?php if(in_folder()):?>
        <tr>
          <td colspan="4"><img src='ic_folder.png' style='width: 20px'/> &nbsp;
            <a href="<?php echo parent_up_url();?>">...</a></td>
        </tr>
      <?php endif;?>
      <?php foreach(get_all_parent_folders() as $folderObject):?>
        <tr>
          <td><img src='ic_folder.png' style='width: 20px'/> &nbsp;
            <a data-id="<?php echo $folderObject["id"];?>" data-type="folder" data-filename="<?php echo $folderObject["name"];?>" class="item" href="<?php echo create_url('filemanager.php',['id'=>$folderObject["id"]]);?>">
              <?php echo $folderObject["name"];?></a>
          </td>
          <td><?php  echo $folderObject["createdOn"];?></td>
          <td>File Folder</td>
          <td></td>
        </tr>
      <?php endforeach ;?>
      <?php foreach (get_all_files() as $fObject):?>
          <tr>
              <td><?php echo get_icon($fObject["file_type"],$fObject["real_name"]);?> &nbsp;
                <a data-id="<?php echo $fObject["id"];?>" data-type="file" data-filename="<?php echo $fObject["real_name"];?>" class="item" target="_blank" href="<?php echo create_file_link($fObject["file_name"]);?>">
                  <?php echo $fObject["real_name"];?></a>
              </td>
              <td><?php  echo $fObject["createdOn"];?></td>
              <td><?php  echo $fObject["file_type"];?></td>
              <td><?php  echo $fObject["file_size"];?></td>
          </tr>
      <?php endforeach; ?>
  </tbody>
</table>
<script>

  var CURRENT_BASE_PATH = "<?php echo get_current_url();?>";
  var IS_DIRECTORY = "<?Php echo in_folder()?>";
  $(document).ready(function(){

    rightClickEvents();

    function rightClickEvents(){
      $.contextMenu({
        // define which elements trigger this menu
        selector: ".item",
        // define the elements of the menu
        items: {
          "rename": {name: "Rename",  icon:'add', callback: function(key, opt){
              var id = $(this).attr('data-id');
              var ftype = $(this).attr('data-type');
              var fname = $(this).attr('data-filename');
              renamePrompt(id, ftype, fname);
            }},
          "delete": {name: "Delete", icon:'delete', callback: function(key, opt){
              var id = $(this).attr('data-id');
              var ftype = $(this).attr('data-type');
              var fname = $(this).attr('data-filename');
               deleteItem(id, ftype);
            }}
        }
        // there's more, have a look at the demos and docs...
      });
    }

    function renamePrompt(id, filetype, filename){
      if(IS_DIRECTORY){
        var formAction = CURRENT_BASE_PATH + "&"+filetype+"=" + id;
      }else{
        var formAction = CURRENT_BASE_PATH + "?"+filetype+"=" + id;
      }
      $.confirm({
        useBootstrap: false,
        title: 'Rename '+ filename,
        content: '' +
          '<form action="'+formAction+'" class="formName">' +
          '<div class="form-group">' +
          '<label>Rename '+filetype+' to</label>' +
          '<input type="text" placeholder="name" class="name form-control" required />' +
          '</div>' +
          '</form>',
        buttons: {
          formSubmit: {
            text: 'Submit',
            btnClass: 'btn-blue',
            action: function () {
              var name = this.$content.find('.name').val();
              if(!name){
                $.alert('provide a valid name');
                return false;
              }else{
                formAction += "&name=" + name;
                window.location.href = formAction;
              }
            }
          },
          cancel: function () {
            //close
          },
        },
      });
    }

    function deleteItem(item, itemtype){
      if(confirm("Are you sure you want to delete this item")){
        if(IS_DIRECTORY){
          var deleteAction = CURRENT_BASE_PATH + "&type="+itemtype+"&item="+item+'&action=delete';
        }else{
          var deleteAction = CURRENT_BASE_PATH + "?type="+itemtype+"&item=" + item+'&action=delete';
        }
        window.location.href = deleteAction;
      }
    }

  });
</script>

</body>
</html>



