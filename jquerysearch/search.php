<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ajax Insert and Search Task List Example App</title>
    <script
    src="https://code.jquery.com/jquery-3.5.1.min.js"
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
    crossorigin="anonymous"></script>
</head>
<body>
<p>Add an item</p>
<input id="task-input" type="text" placeholder="Enter task"/>
<button id="save-task">Save</button>
<button id="search">Search</button>
<button id="clear">Clear</button>
<ul id="tasks">
</ul>
<script>
    $(document).ready(function(){

        var taskEl = $('#task-input');
        var taskList = $('#tasks');

        $('#save-task').on("click", function(){
            var task = taskEl.val();
            saveTask(task);
            console.log("save button clicked: the task is " + task);
            return false;
        });

        $("#task-input").keypress(function(e){
           if( e.which === 13){
               var task = taskEl.val();
               saveTask(task);
               return false;
           }
        });

        $('#tasks').on('click', '.remove-task', function(){
            var el = $(this);
            var id = el.attr('data-id');
            if(id){
                $.post('server.php?action=remove_task', {id:id}, function(data){
                    getItems();
                });
            }
        });

        $('#search').on("click", function(){
            var query = taskEl.val();
            $.get("server.php?action=search_items", {query:query}, function(data){
                taskList.empty().append(data);
            })
            return false;
        });

        $('#clear').on("click", function(){
           taskEl.val("");
           getItems();
        });

        function saveTask(task){
            $.post('server.php?action=save_task', {task:task}, function(resp){
                console.log(resp);
                taskEl.val("");
                getItems();
            });
        }

        function getItems(){
            $.get("server.php?action=get_items", function(data){
                taskList.empty().append(data);
            });
        }

        getItems();

    });
</script>
</body>
</html>