<?php
// https://dimsemenov.com/plugins/magnific-popup/
// https://www.dropzonejs.com/#installation
// https://swisnl.github.io/jQuery-contextMenu/demo/trigger-left-click.html
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gallery</title>
    <link rel="stylesheet" href="./magnific-popup.css">
    <link rel="stylesheet" href="./dropzone.css">
    <link rel="stylesheet" href="./jquery.contextMenu.min.css">
    <script
            src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
            crossorigin="anonymous"></script>
    <script src="./jquery.magnific-popup.min.js"></script>
    <script src="./dropzone.js"></script>
    <script src="./jquery.ui.position.min.js"></script>
    <script src="./jquery.contextMenu.min.js"></script>
</head>
<style>
    body {
        background: #0a0a0a;
        color: whitesmoke;
    }
    .white-popup-block {
        background: #FFF;
        padding: 20px 30px;
        text-align: left;
        max-width: 650px;
        margin: 40px auto;
        position: relative;
        color: black;
    }
    .gallery-block {
        width: 160px;
        height: 150px;
        position: relative;
        margin-left: 3px;
        margin-bottom: 7px;
        cursor: pointer;
    }

    .gallery-block:hover:after {
        content:"\A";
        width: 160px;
        height: 155px;
        background:rgba(0, 0, 0, 0.5);
        position:absolute;
        top:0;
        left:0;
    }

    .gallery-block input {
        text-align: left;
        position: absolute;
        top: 1px;
        z-index: 1000;
    }

    .gallery-img {
        background-color: #cccccc;
        width: 150px;
        height: 150px;
        background-position: center; /* Center the image */
        background-repeat: no-repeat;
        background-size: cover;
        border-radius: 3px;
        border: 3px solid #fff;
    }



    .gallery-container .gallery-block {
        display: inline-block;
    }

    .gallery-container {
        margin-top: 5px;
    }

</style>
<body>
<h3>Gallery Manager</h3>
<a href="javascript:void(0);" id="add-files-to-gallery">Add Images</a>
&nbsp; &nbsp;
Add New Gallery : <input id="gallery-name" type="text"/>
<button id="save-gallery">Save</button>
&nbsp; &nbsp;
Galleries:
<div id="folders-containers" style="display: inline-block;">
    <select><option>All</option></select>
</div>
<button id="view-gallery">View</button>
<button id="switch-gallery">Switch Gallery</button>
<div id="gallery" class="gallery-container">
</div>
</body>
<div id="add-to-gallery-modal" class="white-popup-block mfp-hide">
    <h2>Add Images</h2>
    <a id="delete-history" href="javascript:void(0);">Delete history</a>
    <div style="height: 300px;">
        <form action="gallery_requests.php?action=upload" class="dropzone">
        </form>
    </div>
</div>
<script>
    var myDropZone = null;


    function getImages(gallery){
        if(gallery === undefined){
            gallery = "";
        }
        $.get("gallery_requests.php?action=get-files", {gallery:gallery},function(res){
            $("#gallery").empty().append(res);
            leftClickEvents();
        })
    }

    function getFolders(){
        $.get("gallery_requests.php?action=get-folders", function(res){
            $("#folders-containers").empty().append(res);
        })
    }

    function leftClickEvents(){
        $.contextMenu({
            // define which elements trigger this menu
            selector: ".gallery-block",
            // define the elements of the menu
            items: {
                "share": {name: "Share",  icon:'add', callback: function(key, opt){
                        alert($(this).attr('data-url'));
                    }},
                "delete": {name: "Delete", icon:'delete', callback: function(key, opt){
                        var id = $(this).attr('data-id');
                        deleteGalleryImage(id);
                    }}
            }
            // there's more, have a look at the demos and docs...
        });
    }

    function deleteGalleryImage(id){
        $.post("gallery_requests.php?action=delete-file",{id:id}, function(res){
            if(res == "good"){
                getImages();
            }
        });
    }

    function init(){
        getImages();
        getFolders();
    }



    $(document).ready(function(){

        $("#delete-history").on("click", function(){
            if(myDropZone){
                myDropZone.removeAllFiles(); 
            }
            return false;
        });

        $("#add-files-to-gallery").on("click", function(){
            $.magnificPopup.open({
                items : {
                    src: '#add-to-gallery-modal'
                },
                type: 'inline',
                enableEscapeKey: false,
            },0);
            return false;
        });

        $('#gallery').on("click", '.gallery-block', function(event){
            var target = $(event.target);
            if(target.is(".gallery-block")){
                var url = $(this).attr('data-url');
                $.magnificPopup.open({
                    items: {
                        src: url
                    },
                    type: 'image',
                    enableEscapeKey: false,
                }, 0);
                return false;
            }
        });

        $('#save-gallery').on("click", function(){
            var gname = $('#gallery-name').val();
            if(gname){
                $.post("gallery_requests.php?action=save-gallery", {gallery: gname},function(res){
                    if(res == "good"){
                        // was successfullly
                        $("#gallery-name").val("");
                        getFolders();
                    }
                });
            }
            return false;
        });

        $("#view-gallery").on("click", function(){
            var currentGallery = $('#folders-list').val();
            getImages(currentGallery);
            return false;
        });

        $("#switch-gallery").on("click", function(){
            var currentGallery = $('#folders-list').val();
            var val = [];
            $('input[name=\"gid[]\"]:checked:enabled').each(function(i){
                val[i] = $(this).val();
            });
            if(val.length == 0){
                alert("Please select at least on record");
            }else{
                var ids = val.join(', ');
                $.post("gallery_requests.php?action=switch-gallery",
                    {ids:ids, gallery:currentGallery}, function(res){
                    if(res == "good"){
                        getImages();
                    }
                })
            }
            return false;
        });

        init();

    });

    myDropZone = new Dropzone(".dropzone", {
        acceptedFiles: 'image/*',
        dictDefaultMessage: `<style>
        .message{text-align:center;margin:0 auto;line-height:47px}.msg-title{font-size:20px}.msg-option{font-size:12px}.msg-button{border-radius:2px;font-size:17px;height:27px;min-width:54px;outline:0;padding:15px;margin-bottom:5px;text-align:center;white-space:nowrap;border:1px solid rgba(0,0,0,.1);color:#444;background-color:#F1F1F1;cursor:pointer}
        </style>
        <div class='message'>
        <span class='msg-title'>Drop files anywhere to upload</span></br>
        <span class='msg-option'>or</span></br>
        <span class='msg-button'>Select Files</span></br>
        <span>Maximum Upload Size: 3 mb</span>
        </div>`,
        maxFilesize: 3, // MB
        init: function () {
            this.on("complete", function (file) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    // do something here
                    getImages();
                }
            });
        }
    });

</script>
</html>
