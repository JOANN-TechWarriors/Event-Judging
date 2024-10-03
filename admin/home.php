<?php
// Include necessary files and start the session
include('header2.php');
include('session.php');
include('dbcon.php'); // Include your PDO database connection file



// Handle form submission to create a new event
if (isset($_POST['create'])) {
    $event_name = $_POST['main_event'];
    $event_start_date = $_POST['date_start'];
    $event_end_date = $_POST['date_end'];
    $event_time = $_POST['event_time'];
    $event_venue = $_POST['place'];
    $banner = $_FILES['banner']['name'];
    $target = "../img/" . basename($banner);

    // Insert event details into the database using PDO
    $sql = "INSERT INTO main_event (event_name, status, organizer_id, date_start, date_end, place, banner) 
            VALUES (:event_name, 'deactivated', :organizer_id, :date_start, :date_end, :place, :banner)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':event_name', $event_name);
    $stmt->bindParam(':organizer_id', $session_id);
    $stmt->bindParam(':date_start', $event_start_date);
    $stmt->bindParam(':date_end', $event_end_date);
    $stmt->bindParam(':place', $event_venue);
    $stmt->bindParam(':banner', $banner);

    if ($stmt->execute()) {
        if (move_uploaded_file($_FILES['banner']['tmp_name'], $target)) {
            $_SESSION['message'] = 'Event created successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Event created, but failed to upload banner.';
            $_SESSION['message_type'] = 'error';
        }
    } else {
        $_SESSION['message'] = 'Failed to create event.';
        $_SESSION['message_type'] = 'error';
    }

    header("Location: home.php"); // Redirect to the same page or another page
    exit();
}

// Fetch events from the database using PDO
$query = "SELECT * FROM main_event WHERE organizer_id = :organizer_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':organizer_id', $session_id);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="shortcut icon" href="../images/logo copy.png"/>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #fff;
        margin: 0;
        padding: 0;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 250px;
        background-color: #27293d;
        color: #fff;
        padding-top: 20px;
        transition: all 0.3s;
        overflow: hidden;
    }

    .sidebar.collapsed {
        width: 80px;
    }

    .sidebar .toggle-btn {
        position: absolute;
        top: 10px;
        right: -2px;
        background-color: transparent;
        color: #fff;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 20px;
    }

    .sidebar .toggle-btn i {
        font-size: 18px;
    }

    .sidebar-heading {
        text-align: center;
        padding: 10px 0;
        font-size: 18px;
        margin-bottom: 10px;
    }

    .sidebar-heading img {
        max-width: 100px;
        max-height: 100px;
    }

    .sidebar ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .sidebar ul li {
        padding: 10px;
        border-bottom: 1px solid #555;
        transition: all 0.3s;
    }

    .sidebar ul li a {
        color: #fff;
        text-decoration: none;
        display: flex;
        align-items: center;
    }

    .sidebar ul li a i {
        margin-right: 10px;
        transition: margin 0.3s;
    }

    .sidebar.collapsed ul li a i {
        margin-right: 0;
    }

    .sidebar ul li a span {
        display: inline-block;
        transition: opacity 0.3s;
    }

    .sidebar.collapsed ul li a span {
        opacity: 0;
        width: 0;
        overflow: hidden;
    }

    .sidebar ul li a:hover {
        background-color: #555;
    }

    .main {
        margin-left: 250px;
        padding: 20px;
        transition: all 0.3s;
    }

    .main.collapsed {
        margin-left: 80px;
    }

    .header {
        background-color: #f8f9fa;
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #ddd;
    }

    .header .profile-dropdown {
        position: relative;
        display: inline-block;
    }

    .header .profile-dropdown img {
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
    }

    .header .profile-dropdown .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        background-color: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        overflow: hidden;
        z-index: 1000;
    }

    .header .profile-dropdown:hover .dropdown-menu {
        display: block;
    }

    .header .profile-dropdown .dropdown-menu a {
        display: block;
        padding: 10px;
        color: #333;
        text-decoration: none;
    }

    .header .profile-dropdown .dropdown-menu a:hover {
        background-color: #f1f1f1;
    }

    .tile-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .tile {
        background-color: #999999;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .tile i {
        font-size: 50px;
        margin-bottom: 10px;
    }

    .tile h3 {
        margin: 10px 0;
    }

    .tile p {
        color: #ddd;
    }

    .tile:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 1024px) {
        .tile-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
        }

        .sidebar.collapsed {
            width: 100%;
        }

        .main {
            margin-left: 0;
        }
    }

    @media (max-width: 576px) {
        .sidebar-heading {
            font-size: 20px;
        }

        .sidebar ul li a {
            font-size: 20%;
        }

        .header {
            padding: 5px 10px;
        }

        .header .profile-dropdown img {
            width: 30px;
            height: 30px;
        }
    }
    .tile {
    position: relative;
    /* ... other existing styles ... */
    }

    .dropdown {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .dropbtn {
        background-color: transparent;
        color: black;
        font-size: 16px;
        border: none;
        cursor: pointer;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        background-color: #f9f9f9;
        min-width: 120px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {background-color: #f1f1f1}

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown:hover .dropbtn {
        background-color: #f1f1f1;
    }
    </style>
</head>

<body>
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" id="toggle-btn">☰</button>
        <div class="sidebar-heading">
            <img src="../img/logo.png" alt="Logo">
            <div>Event Judging System</div>
        </div>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>DASHBOARD</span></a></li>
            <li><a href="home.php"><i class="fas fa-calendar-check"></i> <span>ONGOING EVENTS</span></a></li>
            <li><a href="upcoming_events.php"><i class="fas fa-calendar-alt"></i> <span>UPCOMING EVENTS</span></a></li>
            <li><a href="score_sheets.php"><i class="fas fa-clipboard-list"></i> <span>SCORE SHEETS</span></a></li>
            <li><a href="rev_main_event.php"><i class="fas fa-chart-line"></i> <span>DATA REVIEWS</span></a></li>
            <li><a href="#live.php"><i class="fas fa-chart-line"></i> <span>LIVE</span></a></li>

        </ul>
    </div>
    <div class="main" id="main-content">
        <div class="container">
            <h1>Ongoing Events</h1>
        </div>

        <section id="download-bootstrap">
            <div class="page-header">
                <a data-toggle="modal" class="btn btn-info pull-right" href="#addMEcollapse"
                    title="Click to add Main Event"><i class="icon icon-plus"></i> <strong>EVENT</strong></a>

                <!-- Modal for adding an event -->
                <div id="addMEcollapse" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addMEcollapseLabel" aria-hidden="true">                    
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title"><strong>ADD EVENT</strong><button type="button" class="close"
                                        data-dismiss="modal">&times;</button></h4>
                            </div>
                            <div class="modal-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <strong>Upload Banner:</strong><br />
                                        <input type="file" name="banner" accept="img/*">
                                    </div>
                                    <div class="form-group">
                                        <label for="main_event"><strong>Event Name:</strong></label>
                                        <input type="text" name="main_event" class="form-control btn-block"
                                            style="text-indent: 5px !important; height: 30px !important;"
                                            placeholder="Event Name" required />
                                    </div>
                                    <div class="form-group">
                                        <label for="date_start"><strong>Start Date:</strong></label>
                                        <input type="date" id="date_start" name="date_start" class="form-control btn-block"
                                            style="text-indent: 5px !important; height: 30px !important;" required />
                                    </div>
                                    <div class="form-group">
                                        <label for="date_end"><strong>End Date:</strong></label>
                                        <input type="date" id="date_end" name="date_end" class="form-control btn-block"
                                            style="text-indent: 5px !important; height: 30px !important;" required />
                                    </div>
                                   <div class="form-group">
                                        <label for="event_time"><strong>Time:</strong></label>
                                        <input type="time" id="event_time" name="event_time" class="form-control btn-block"
                                               style="text-indent: 5px !important; height: 30px !important;" 
                                               step="1800" required />
                                    </div>
                                    <div class="form-group">
                                        <label for="place"><strong>Venue:</strong></label>
                                        <input type="text" name="place" class="form-control btn-block"
                                            style="text-indent: 5px !important; height: 30px !important;"
                                            placeholder="Venue" required />
                                    </div>
                                    <div class="modal-footer">
                                        <button name="create" class="btn btn-success"><i class="icon-save"></i>
                                            <strong>SAVE</strong></button>
                                        <button type="reset" class="btn btn-default"><i class="icon-ban-circle"></i>
                                            <strong>RESET</strong></button>
                                        
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <br> <br><br>
                <!-- Display events -->
                <div class="tile-container">
    <?php foreach ($events as $event) { ?>
    <div class="tile" data-id="<?php echo htmlspecialchars($event['mainevent_id']); ?>">
        <div class="dropdown">
            <button class="dropbtn">&#8942;</button>
            <div class="dropdown-content">
                <a href="#editEvent" class="btn btn-success edit-event" data-id="<?php echo htmlspecialchars($event['mainevent_id']); ?>">
                    <i class="icon-pencil"></i> Edit
                </a>
                <a href="#" class="btn btn-danger delete-event" data-id="<?php echo htmlspecialchars($event['mainevent_id']); ?>">
                    <i class="icon-remove"></i> Delete
                </a>
            </div>
        </div>
        <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
        <p><?php echo date('m-d-Y', strtotime($event['date_start'])); ?> to
        <?php echo date('m-d-Y', strtotime($event['date_end'])); ?></p>
        <p><?php echo htmlspecialchars($event['place']); ?></p>
    </div>
    <?php } ?>
</div>


        </section>
    <script src="..//assets/js/jquery.js"></script>
    <script src="..//assets/js/bootstrap-transition.js"></script>
    <script src="..//assets/js/bootstrap-alert.js"></script>
    <script src="..//assets/js/bootstrap-modal.js"></script>
    <script src="..//assets/js/bootstrap-dropdown.js"></script>
    <script src="..//assets/js/bootstrap-scrollspy.js"></script>
    <script src="..//assets/js/bootstrap-tab.js"></script>
    <script src="..//assets/js/bootstrap-tooltip.js"></script>
    <script src="..//assets/js/bootstrap-popover.js"></script>
    <script src="..//assets/js/bootstrap-button.js"></script>
    <script src="..//assets/js/bootstrap-collapse.js"></script>
    <script src="..//assets/js/bootstrap-carousel.js"></script>
    <script src="..//assets/js/bootstrap-typeahead.js"></script>
    <script src="..//assets/js/bootstrap-affix.js"></script>
    <script src="..//assets/js/holder/holder.js"></script>
    <script src="..//assets/js/google-code-prettify/prettify.js"></script>
    <script src="..//assets/js/application.js"></script>
    <!-- SweetAlert JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['message'])): ?>
        Swal.fire({
            title: '<?php echo htmlspecialchars($_SESSION['message']); ?>',
            icon: '<?php echo $_SESSION['message_type'] === 'success' ? 'success' : 'error'; ?>',
            confirmButtonText: 'OK'
        }).then(() => {
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        });
        <?php endif; ?>

        document.getElementById('logout').addEventListener('click', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure you want to log out?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to logout.php
                    window.location.href = 'logout.php';
                }
            });
        });

        $('#toggle-btn').on('click', function() {
            $('#sidebar').toggleClass('collapsed');
            $('#main-content').toggleClass('collapsed');
            $(this).toggleClass('collapsed');
        });
    });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tile').forEach(function(tile) {
        tile.addEventListener('click', function() {
            var eventId = this.getAttribute('data-id');
            window.location.href = 'sub_event.php?id=' + eventId;
        });
    });
});

    </script>
    <script>
    // Function to get today's date in YYYY-MM-DD format
    function getTodayDate() {
        return new Date().toISOString().split('T')[0];
    }

    // Set min attribute for both date inputs
    document.getElementById('date_start').min = getTodayDate();
    document.getElementById('date_end').min = getTodayDate();

    // Ensure end date is not before start date
    document.getElementById('date_start').addEventListener('change', function() {
        document.getElementById('date_end').min = this.value;
    });
    document.getElementById('event_time').addEventListener('change', function() {
        var time = this.value;
        var [hours, minutes] = time.split(':');
        
        // Round minutes to nearest 30
        minutes = (Math.round(minutes / 30) * 30) % 60;
        
        // Adjust hours if minutes rounded up to 60
        if (minutes === 0 && parseInt(this.value.split(':')[1]) > 30) {
            hours = (parseInt(hours) + 1) % 24;
        }
        
        // Format hours and minutes to ensure two digits
        hours = hours.toString().padStart(2, '0');
        minutes = minutes.toString().padStart(2, '0');
        
        this.value = `${hours}:${minutes}`;
    });
</script>
</body>
</html>
