<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="./fullcalendar.bundle.css">
    <link rel="stylesheet" href="./magnific-popup.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script
            src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
            crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="./fullcalendar.bundle.js"></script>
    <script src="./jquery.magnific-popup.min.js"></script>

</head>
<style>
    .white-popup-block{
        background: #fff;
        padding: 20px 30px;
        text-align: left;
        max-width: 650px;
        margin: 40px auto;
        position: relative;
    }
</style>
<body>
<h3>New Year Resolution Tracker</h3>
<br><br>
<div>
    <div id="calendar"></div>
</div>

<div id="add-goal-modal" class="white-popup-block mfp-hide">
    <p style="height: 30px; background: darkblue; width: 100%;"></p>
    <h2>Add New Goal</h2>
    <div style="min-height: 250px;">
        <form id="save-goal-form" method="post">
            <input type="hidden" name="id"/>
            Name: <input  type="text" name="goal"/><br>
            Date to Start: <input id="sd" type="text" name="startdate" autocomplete="off"/><br>
            Description : <input type="text" name="description"/><br>
            Status : <select name="status">
                <option value="pending">Pending</option>
                <option value="inprogress">In Progress</option>
                <option value="completed">Completed</option>
            </select><br>
            Notes:<br>
            <textarea cols="55" rows="5" name="notes"></textarea><br>
            <button type="submit" name="save-goal">Save Goal</button>
        </form>
    </div>
    <p style="height: 5px; background: darkblue; width: 100%; margin-top: 10px;"></p>
</div>

<div id="show-goal-modal" class="white-popup-block mfp-hide">
    <p style="height: 30px; background: orangered; width: 100%;"></p>
    <h2 id="goal-date">Thursday 25th December 2020</h2>
    <p style="float: right; margin-right: 3px; padding: 5px; background: darkred; color:white;">Pending</p>
    <div style="min-height: 250px;">
        <h3 id="goal-title">This is my goal</h3>
        <p id="goal-description">This is my goal description</p>
        <a id="edit-goal-action" href="#" style="font-size: 12px;">Edit me</a>
        <p>Remind me :
            <a href="#" class="rm" data-id="day">One Day</a> |
            <a href="#" class="rm" data-id="week">One Week</a> |
            <a href="#" class="rm" data-id="month">One Month</a> |
            <a href="#" class="rm" data-id="year">One Year</a> |
        </p>
        <p id="goal-notes" style="padding: 10px; background: #3F3F3F; color: white; border-radius: 3px; min-height: 100px;">
            test test test test test test
        </p>
        <div style="display: none;" id="save-notes-layout">
            <textarea id="goal-notes-input" cols="55" rows="6"></textarea>
            <br>
            <button id="save-notes">Save notes</button>
        </div>
        <p>Update Progress :
            <a href="#" class="cp" data-id="pending">Pending</a> |
            <a href="#" class="cp" data-id="inprogress">In Progress</a> |
            <a href="#" class="cp" data-id="completed">Completed</a> |
        </p>
        <p style="padding: 10px; background:lightcoral; font-weight: bold;">Remove this event? <a href="#" id="delete-goal">Click to Delete</a></p>
    </div>
    <p style="height: 5px; background: orangered; width: 100%; margin-top: 10px;"></p>
</div>

<script>

    var calendar = null;
    var currentEvent = null;

    $(document).ready(function(){
        $('#sd').datepicker({ dateFormat: 'yy-mm-dd' });

        $('#save-goal-form').submit(function(event){
           var data = $(this).serialize();
            saveGoal(data);
            $.magnificPopup.close();
            getEvents();
            $(this).trigger('reset');
            $('#save-goal-form input[name=id]').val("");
            event.preventDefault();
        });

        $('#goal-notes').on("click", function(){
            $('#save-notes-layout').show();
            $(this).hide();
        });

        $('#save-notes').on('click', function(){
            var notes = $("#goal-notes-input").val();
            $.post('server.php?action=save-notes',{id:currentEvent,notes:notes}, function(res){
                $('#goal-notes').html(res.notes);
                $('#goal-notes').show();
                $('#save-notes-layout').hide();
            })
        });

        $('.cp').on('click', function(){
            var pg = $(this).attr('data-id');
            $.post('server.php?action=update-progress',{id:currentEvent, status:pg}, function(res){
                if(res == "good"){
                    getEvents();
                }
            });
        });

        $('.rm').on('click', function(){
            var next = $(this).attr('data-id');
            $.post('server.php?action=next-date',{id:currentEvent, next:next}, function(res){
               if(res == 'good'){

                   getEvents();
               }
            });
        });

        $('#delete-goal').on('click', function(){
            if(currentEvent){
                var check = confirm("Are you sure you want to delete this goal?");
                if(check){
                    $.post('server.php?action=remove-goal',{id:currentEvent}, function(res){
                        if(res=='good'){
                            $.magnificPopup.close();
                            getEvents();
                        }
                    })
                }
            }
            return false;
        });

        $('#edit-goal-action').on('click', function(){
            // close any open popups
            $.magnificPopup.close();

            $.get('server.php?action=get-event',{id:currentEvent}, function(event){
                // assign event elements
                $('#save-goal-form input[name=id]').val(event.id);
                $('#save-goal-form input[name=goal]').val(event.title);
                $('#save-goal-form input[name=description]').val(event.description);
                $('#save-goal-form input[name=startdate]').val(event.start);
                $('#save-goal-form textarea[name=notes]').val(event.notes);
                $('#save-goal-form select[name=status]').val(event.status);
                // then we open the modal
                $.magnificPopup.open({
                    items: {
                        src : "#add-goal-modal"
                    },
                    type: 'inline',
                    enableEscapekey: false
                },0);
            });

        });

        getEvents(); // get events and load calendar
    });

    function getEvents(){
        $.get('server.php?action=get-events',function(events){
            console.log(events);
            loadCalendar(events);
        });
    }


    function saveGoal(data){
        $.post('server.php?action=save-goal', data, function(res){
            console.log(res);
        });
    }

    function showGoal(id){
        // get event from server
        $.get('server.php?action=get-event',{id:id}, function(event){
            // assign event elementst
            $('#goal-title').text(event.title);
            $('#goal-description').text(event.description);
            $('#goal-date').text(event.dateFormatted);
            $('#goal-notes').html(event.notesFormatted);
            $('#goal-notes-input').text(event.notes);
            // then we open the modal
            $.magnificPopup.open({
                items: {
                    src : "#show-goal-modal"
                },
                type: 'inline',
                enableEscapekey: false
            },0);
        });
    }

    function loadCalendar(data){
        var calendarEl = document.getElementById('calendar');
        if(calendar){
            calendar.destroy();
        }
        calendar = new FullCalendar.Calendar(calendarEl,{
            events: data,
            plugins: ['interaction','dayGrid','timeGrid','list'],
            header:{
                left:'prev, next today',
                center: 'title',
                right: 'dayGridMonth, timeGridWeek, timeGridDay'
            },
            dateClick: function(info){
                //alert("click here" + info.dateStr);
                createGoal(info.dateStr);
            },
            eventClick: function(info){
                currentEvent = info.event.id;
                showGoal(info.event.id);
            }
        });
        calendar.render();
    }



    function createGoal(currentDate){
        $('#save-goal-form').trigger('reset');
        $('#save-goal-form input[name=id]').val("");
        $('#sd').val(currentDate);
        $.magnificPopup.open({
            items: {
                src : "#add-goal-modal"
            },
            type: 'inline',
            enableEscapekey: false
        },0);
    }

</script>
</body>
</html>