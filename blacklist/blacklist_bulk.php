<?php

#################################################################################################### --- ERRORS
error_reporting(0);
session_start();
#######################################################################################################
include_once("config/app_config.php");
if (!isset($_SESSION['login_user'])) {
    header('Location: index.php'); // Redirecting To Home Page
}
if (!isset($_SESSION['access']) || $_SESSION['access'] === 'client') {
    http_response_code(404);
    die();
}
##################################################################ALLOW ACCESS ONLY TO CERTAIN USERS#############################################

?>
<!-- #################################################################################################### --- HTML HEADER -->

<!DOCTYPE html>
<html>
<!--<head>-->

<?php include("head.php"); ?>
<?php echo "<script>window.onload=function(){\$('[data-toggle=\"tooltip\"]').tooltip({'html': true});}</script>"; ?>


<!--</head>-->

<body>
<?php include("navigation.php"); ?>

<script type="text/javascript" charset="utf8" src="/billing-system/resources/js/popper.min.js"></script>
<link rel="stylesheet" type="text/css" href="/billing-system/resources/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="/billing-system/resources/css/responsive.dataTable.min.css">
<link rel="stylesheet" type="text/css" href="/billing-system/resources/css/jquery-confirm.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="/billing-system/resources/css/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@500&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/e2f8401cee.js" crossorigin="anonymous"></script>

<style>
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    /* Define the custom class for a bigger Swal prompt */
    .bigger-swal-prompt {
        font-size: 15px; /* Adjust the font size as needed */
    }
    .container-custom-border {
        border: 2px ridge #e3f4fa;
        /*background: #eaeaea;*/
        background: rgb(156, 218, 220);
        background: linear-gradient(0deg, rgb(163, 237, 238) 0%, rgb(212, 229, 248) 0%, rgba(232,232,232,1) 29%, rgb(238, 245, 250) 73%);
        padding: 10px;
    }
    .container-custom-border-table {
        border: 2px ridge #d7f0f8;
        background: rgb(198, 235, 236);
        background: linear-gradient(0deg, rgb(198, 235, 236) 0%, rgba(223,223,223,1) 0%, rgba(255,255,255,1) 45%,rgb(134 179 207 / 16%) 100%);;
        padding: 10px;
    }
    .padding_left_right {
        padding-left: 0px !important;
        padding-right: 0px !important;
    }
    .animate-charcter {
        text-transform: uppercase;
        background-image: linear-gradient(-225deg,
        #595858 0%,
        #495b6b 29%,
        #5ba1c4 67%,
        #354356 100%);
        background-size: auto auto;
        background-clip: border-box;
        background-size: 50% auto;
        color: #fff;
        background-clip: text;
        /* text-fill-color: transparent; */
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: textclip 10s linear infinite;
        display: inline-block;
        font-size: 35px;
        font-weight: 900;
        margin-left: 5%;
    }
</style>

<!-- Modal -->
<div id="blacklistModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Checked Numbers found in blacklists</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="blacklistNumbers"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- View Cron Jobs  -->
<div class="modal fade" id="cronjobs" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Add Blacklist</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-result">
                    <label for="add_blacklist">Blacklist </label>
                    <input type="text" class="form-control" id="add_blacklist" placeholder="Enter Blacklist Name">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal"  id="add_blacklist_button">Add Blacklist</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- CMS Information  -->
<div class="modal fade" id="cms_info" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">About Blacklist</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div id="modal-result">

                    <p><strong>General Information about Blacklist Functionality:</strong><br>

                    </p>
                    <p>In the table below, you can check the content of blacklists through <strong>Show blacklist content</strong> and you can delete their content through <strong>Delete blacklist content</strong>.
                        In the dropdown below you have to select a blacklist and for this blacklist you can do some actions such as<br>
                        <strong>Check number in blacklist:</strong> To check if a number exists in all blacklists<br>
                        <strong>Add number to Blacklist:</strong> You can add only one number to the blacklist<br>
                        <strong>Check file data in blacklists:</strong> You can select a file and check if the numbers in this file exist in all blacklists and before uploading a file you can check it if there is any number not in the correct format.<br>
                        <strong>  Add OR Delete file data in Blacklist:</strong> You can add many numbers to the blacklist by uploading a file or you can delete them.</p><br>

                    File should be in <strong>.txt</strong> format and numbers should be one after the other like this:</p>
                    039447568705<br>
                    058447568706<br>
                    +058447568706


                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!--ADD/EDIT/DELETE BLACKLIST -->
<!-- Modal -->
<div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Blacklist Content</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <button class="btn btn-block btn-primary" type="button" data-toggle="collapse" data-target="#collapseWidthExample" aria-expanded="false" aria-controls="collapseWidthExample">
        Click to Show,Add and Delete blacklist below
        <i class="fa fa-angle-down"></i>
    </button>

    <div class="collapse" id="collapseWidthExample">
        <div class="well">
            <div id="response"></div>
            <button type="button" class="btn btn-primary" id="cms_info"  data-toggle="modal" data-target="#cms_info">
                <span class="glyphicon glyphicon-info-sign"></span>  About Blacklist
            </button>
            <button type="button" class="btn btn-primary" id="cronjobs"  data-toggle="modal" data-target="#cronjobs">
                <span class="glyphicon glyphicon-plus"></span> Add Blacklist
            </button>

            <div class=" padding_left_right" >
                <p id="global_errors" class="input-error"></p>
                <div id="statements" class="panel panel-default container-custom-border-table">
                    <table id="blacklist_table" class="table table-bordered" style="width:100%">
                        <thead>
                        <tr>
                            <th>ID </th>
                            <th>Blacklist name</th>
                            <th>Date time</th>
                            <th>Action</th>

                        </tr>
                        <tr>
                            <th>ID </th>
                            <th>Blacklist name</th>
                            <th>Date time</th>
                            <th>Action</th>

                        </tr>
                        </thead>
                        <thead class="filters">

                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<br>

<!-- #################################################################################################### --- DATATABLE -->
<div class="container mt-5 panel panel-default container-custom-border">
    <div class="form-group">
        <div class="col-md-12 text-center" style="margin-bottom: 15px;">
            <h3 class="animate-charcter">Blacklist Bulk</h3>

        </div>
        <label for="blacklistSelect">Select Blacklist:</label>
        <select id="blacklistSelect" class="form-control"></select>
    </div>
    <br>

    <div class="row">
        <div class="form-group">
            <div class="col-md-6">
                <label for="checkNumbertoBlacklist">Check number in blacklists:</label>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search number in all blacklists" id="checkNumbertoBlacklist">
                    <div class="input-group-btn">
                        <button class="btn btn-primary" type="submit" id="checkNumbertoBlacklist_button"  title="Checks if number is in all blacklists"><i class="glyphicon glyphicon-search"></i></button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label for="checkNumberInBlacklist">Add number to Blacklist:</label>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Add number to the selected blacklist" id="addNumbertoBlacklist">
                    <div class="input-group-btn">
                        <button class="btn btn-primary" type="submit" id="addNumbertoBlacklist_button" title="Adds number in  selected blacklist"><i class="glyphicon glyphicon-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="form-group">
            <div class="col-md-6">

                <label for="checkFile" class="form-label">Check file data in blacklists:</label>
                <div class="input-group">
                    <input class="form-control" type="file" id="checkFile">
                    <div class="input-group-btn">
                        <button class="btn btn-primary" id="checkFile_button" title="Checks if any data on the file is already in  selected blacklist"><i class="glyphicon glyphicon-check"></i></button>
                    </div>
                </div>

            </div>

            <div class="col-md-6">
                <label for="uploadFile">Add or Delete File Data in Blacklist:</label>
                <div class="input-group">
                    <input class="form-control" type="file" id="uploadFile">
                    <div class="input-group-btn">
                        <button class="btn btn-primary"  id="uploadFile_button" title="Upload and add all data from the file in selected blacklist"><i class="glyphicon glyphicon-save"></i></button>
                        <button class="btn btn-danger"  id="deleteFile_button" title="Deletes all data from the file in selected blacklist"><i class="glyphicon glyphicon-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 d-flex justify-content-start">
            <!--            <div class="form-group text-center" style="margin-bottom: 15px;" >-->
            <div class="form-group">
                <!--                <button id="filter_statements" class="btn btn-primary"><span class=" glyphicon glyphicon-filter"> </span> Filter Statements</button>-->
                <!--                <button id="export_statements" class="btn btn-success"><span class=" glyphicon glyphicon-export"> </span> Export Statements</button>-->
                <!--                <button id="pdf_statements" class="btn btn-danger"><span class="glyphicon glyphicon-save-file"> </span> PDF </button>-->
            </div>
        </div>
    </div>
</div>




<!-- #################################################################################################### --- FOOTER -->
<script type="text/javascript" charset="utf8" src="/billing-system/resources/js/alertify.min.js"></script>
<script type="text/javascript" charset="utf8" src="/billing-system/resources/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="/billing-system/resources/js/jquery-confirm.min.js"></script>
<script type="text/javascript" charset="utf8" src="/billing-system/resources/js/jquery-ui.min.js"></script>
<script type="text/javascript" charset="utf8" src="/billing-system/resources/js/decimal.min.js"></script>
<script type="text/javascript" charset="utf8" src="/billing-system/resources/js/loadingoverlay.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" src="/billing-system/resources/js/services/BlacklistServices.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $('#add_blacklist_button').click(function() {
        var table= $('#blacklist_table').DataTable();

        var blacklistName = $('#add_blacklist').val();
        $.ajax({
            url: BlacklistServices.add_blacklist,  // Replace with your PHP file's path
            type: 'POST',
            data: { blacklist: blacklistName },
            dataType: 'json',  // Expect a JSON response
            success: function(response) {
                // Handle the JSON response from the PHP file
                if (response.success) {
                    alertify.success(response.message);
                    table.ajax.reload();
                    populateDropdown();
                } else {
                    alertify.error(response.message);
                }
            },
            error: function(xhr, status, error) {
                // Handle any errors that occur during the request
                alertify.error('An error occurred: ' + status + ' ' + error);
            }
        });
    });

    $('#checkNumbertoBlacklist_button').click(function() {
        var number = $('#checkNumbertoBlacklist').val();
        var blacklist = $('#blacklistSelect').val();

        $.ajax({
            url: BlacklistServices.check_in_blacklist,  // Replace with your PHP file's path
            type: 'POST',
            data: { number: number, blacklist: blacklist },
            dataType: 'json',  // Expect a JSON response
            success: function(response) {
                // Handle the JSON response from the PHP file
                if (response.success) {
                    alertify.success(response.message);

                } else {
                    alertify.error(response.message);
                }
            },
            error: function(xhr, status, error) {
                // Handle any errors that occur during the request
                alertify.error('An error occurred: ' + status + ' ' + error);
            }
        });
    });

    $('#addNumbertoBlacklist_button').click(function() {
        var number = $('#addNumbertoBlacklist').val();
        var blacklist = $('#blacklistSelect').val();

        if (!blacklist) {
            alertify.error('Please select a blacklist.');
            return;
        }
        $.ajax({
            url: BlacklistServices.add_in_blacklist,  // Replace with your PHP file's path
            type: 'POST',
            data: { number: number, blacklist: blacklist },
            dataType: 'json',  // Expect a JSON response
            success: function(response) {
                // Handle the JSON response from the PHP file
                if (response.success) {
                    alertify.success(response.message);

                } else if(response.warning){
                    alertify.warning(response.message);
                } else {
                    alertify.error(response.message);
                }
            },
            error: function(xhr, status, error) {
                // Handle any errors that occur during the request
                alertify.error('An error occurred: ' + status + ' ' + error);
            }
        });
    });

    $('#checkFile_button').click(function() {
        var fileInput = $('#checkFile')[0];
        var file = fileInput.files[0];
        var blacklist = $('#blacklistSelect').val();


        if (!file) {
            alertify.error('Please select a file to check.');
            return;
        }

        var formData = new FormData();
        formData.append('file', file);
        formData.append('blacklist', blacklist);
        $.ajax({
            url: BlacklistServices.check_file_in_blacklist,  // Replace with your PHP file's path
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',  // Expect a JSON response
            success: function(response) {
                // Handle the JSON response from the PHP file
                if (response.success) {
                    if (response.html) {
                        $('#blacklistNumbers').html(response.html);
                        $('#blacklistModal').modal('show');
                    } else {
                        alertify.success(response.message);
                    }
                } else {
                    alertify.error(response.message);
                    if (response.invalidNumbers && response.invalidNumbers.length > 0) {
                        alertify.error('Invalid numbers: ' + response.invalidNumbers.join(', '));
                    }
                }
            },
            error: function(xhr, status, error) {
                // Handle any errors that occur during the request
                alertify.error('An error occurred: ' + status + ' ' + error);
            }
        });
    });

    $('#uploadFile_button').click(function() {
        var fileInput = $('#uploadFile')[0];
        var file = fileInput.files[0];
        var blacklist = $('#blacklistSelect').val();

        if (!blacklist) {
            alertify.error('Please select a blacklist.');
            return;
        }
        if (!file) {
            alertify.error('Please select a file to upload.');
            return;
        }

        var formData = new FormData();
        formData.append('file', file);
        formData.append('blacklist', blacklist);

        $.ajax({
            url: BlacklistServices.add_file_in_blacklist,  // Replace with your PHP file's path
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',  // Expect a JSON response
            success: function(response) {
                if (response.success) {
                    alertify.success(response.message);
                    if (response.failed.length > 0) {
                        alertify.error('Failed to add the following numbers: ' + response.failed.join(', '));
                    }
                } else {
                    alertify.error(response.message);
                    if (response.failed.length > 0) {
                        alertify.error('Failed to add the following numbers: ' + response.failed.join(', '));
                    }
                }
            },
            error: function(xhr, status, error) {
                // Handle any errors that occur during the request
                alertify.error('An error occurred: ' + status + ' ' + error);
            }
        });
    });

    $('#deleteFile_button').click(function() {
        var fileInput = $('#uploadFile')[0];
        var file = fileInput.files[0];
        var blacklist = $('#blacklistSelect').val();

        if (!blacklist) {
            alertify.error('Please select a blacklist.');
            return;
        }
        if (!file) {
            alertify.error('Please select a file to upload.');
            return;
        }

        var formData = new FormData();
        formData.append('file', file);
        formData.append('blacklist', blacklist);

        $.ajax({
            url: './api/v1/Blacklist/delete_blacklist_file_content.php',  // Replace with your PHP file's path
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',  // Expect a JSON response
            success: function(response) {
                console.log(response);
                if (response.success) {
                    alertify.success(response.message);
                    if (response.failed.length > 0) {
                        alertify.error('Failed to delete the following numbers: ' + response.failed.join(', '));
                    }
                } else {
                    alertify.error(response.message);
                    if (response.failed.length > 0) {
                        alertify.error('Failed to delete the following numbers: ' + response.failed.join(', '));
                    }
                }
            },
            error: function(xhr, status, error) {
                // Handle any errors that occur during the request
                alertify.error('An error occurred: ' + status + ' ' + error);
            }
        });
    });
    function populateDropdown() {
        $.ajax({
            url: BlacklistServices.get_blacklists,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                var select = $('#blacklistSelect');
                select.empty(); // Clear any existing options
                select.append('<option value="" disabled selected>Select Blacklist</option>');
                $.each(response.data, function(index, item) {
                    select.append('<option value="' + item.name + '">' + item.name + '</option>');
                });
            },
            error: function() {
                alert('Error retrieving blacklist data.');
            }
        });
    }


    $(document).ready(function() {
        var table = $('#blacklist_table').DataTable({
            "processing": true,
            "responsive": true,
            "serverSide": false,
            "paging": true,
            "pageLength": 10,
            "ajax": BlacklistServices.get_blacklists,
            "columns": [
                { "data": "id" },
                { "data": "name" },
                { "data": "datetime" },
                {
                    "data": null,
                    "render": function (data, type, row) {
                        return '<button class="btn btn-success show-btn" data-id="' + row.id + '"><span class="glyphicon glyphicon-eye-open"> </span> Show </button> ' +
                            '<button class="btn btn-danger delete-btn" data-id="' + row.id + '"><span class="glyphicon glyphicon-trash"> </span> Delete </button>';
                    }
                }
            ],
            "initComplete": function () {
                this.api().columns().every(function () {
                    var column = this;
                    var input = $('<input type="text" class="form-control form-control-sm" placeholder="Search...">')
                        .appendTo($(column.header()).empty())
                        .on('keyup change clear', function () {
                            if (column.search() !== this.value) {
                                column.search(this.value).draw();
                            }
                        });
                });
            },
            "ordering": true,
            "createdRow": function(row, data, dataIndex) {
                $(row).find('.old-price').css('background-color', '#ffd6cc'); // Red color
                $(row).find('.new-price').css('background-color', '#ccffcc'); // Green color
            }



        });

        populateDropdown();

    });

    $('#blacklist_table ').on('click', '.show-btn', function() {
        var table= $('#blacklist_table').DataTable();
        var data = table.row($(this).parents('tr')).data();

        var blacklistName = data.name;
        $.ajax({
            url: BlacklistServices.show_blacklist_content,  // PHP file to handle the command execution
            type: 'POST',
            data: { name: blacklistName },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#modal-body').html('<pre>' + response.data + '</pre>');
                    $('#showModal').modal('show');
                } else {
                    alertify.error(response.message);
                }
            },
            error: function(xhr, status, error) {
                alertify.error('An error occurred: ' + status + ' ' + error);
            }
        });
    });

    $('#blacklist_table ').on('click', '.delete-btn', function() {
        var table= $('#blacklist_table').DataTable();
        var data = table.row($(this).parents('tr')).data();

        var blacklistId = data.id;
        var blacklistName = data.name;

        $.ajax({
            url: BlacklistServices.delete_blacklist_content,
            type: 'POST',
            data: { id: blacklistId, name: blacklistName },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alertify.success(response.message);
                    table.ajax.reload();
                    populateDropdown();
                } else {
                    alertify.error(response.message);
                }
            },
            error: function(xhr, status, error) {
                alertify.error('An error occurred: ' + status + ' ' + error);
            }
        });
    });


</script>
</body>

</html>