<?php 
	// include database connection script
	require_once("db-connect-inc.php");
	// include the header file
	require_once("header-inc.php");
	// Update title
	$pagetitle = "View Student Timetable";
	$userId = $_GET["id"];
?>

<!-- main heading -->
<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
    <h1 class="display-4">View Student Timetable</h1>
    <p class="lead">This system allows management of courses, modules, students etc.</p>
</div>

<div class="container">
	<div class="card-deck mb-3 text-left">
		<div class="card mb-4 box-shadow">
			<div class="card-header">
				<h4 class="my-0 font-weight-normal">Timetable</h4>
			</div>
			<div class="card-body">
				<div id="accordion" role="tablist">
					<?php
						$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
						for ($day = 1; $day <=7; $day++) {
							echo "<div class='card'><div class='card-header' role='tab' id='heading" . $day . "'><h5 class='mb-0'>";
							echo "<a class='collapsed' data-toggle='collapse' href='#collapse" . $day . "' aria-expanded='false' aria-controls='collapse" . $day . "'>" . $days[$day-1] . "</a></h5></div>";
							echo "<div id='collapse" . $day . "' class='collapse' role='tabpanel' aria-labelledby='heading" . $day . "'><div class='card-body'>";
							echo "<table class='table stripped'><tr><th>Module</th><th>Building</th><th>Room</th><th>Start</th><th>End</th></tr>";

							try {
								$dayModules = $db->prepare("SELECT booking.start_time, booking.end_time, room.room_number, room.room_name, building.building_name, module.module_name FROM module_particpant INNER JOIN session ON module_particpant.module_id=session.module_id INNER JOIN booking ON session.booking=booking.booking_id INNER JOIN room ON booking.room_id=room.room_id INNER JOIN building ON room.building_id=building.building_id INNER JOIN module ON module_particpant.module_id=module.module_id WHERE module_particpant.user_id = :id AND booking.day = :day");
								$dayModules->bindParam(":id", $userId);
								$dayModules->bindParam(":day", $day);
								$dayModules->execute();

								while ($module = $dayModules->fetch(PDO::FETCH_ASSOC)) {
									echo "<tr>";
									echo "<td>" . $module["module_name"] . "</td><td>" . $module["building_name"] . "</td><td>" . $module["room_name"] . " " . $module["room_number"] . "</td><td>" . $module["start_time"] . "</td><td>" . $module["end_time"];
									echo "</tr>";
								}
							} catch (Exception $error) {
								echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> Unable to fetch timetable data for given day.</div>";
							}

							echo "</table></div></div></div>";
						}
					?>
				</div>
			</div>
		</div>
	</div>

<?php 
    // include the footer file
    require_once("footer-inc.php"); 
?>