<?php 
	// include database connection script
	require_once("db-connect-inc.php");
	// include the header file
	require_once("header-inc.php");
	// Update title
	$pagetitle = "Select Tutor Modules";
	$userId = $_GET["id"];

	$hoursData = $db->prepare("SELECT booking.start_time, booking.end_time FROM module_particpant INNER JOIN session ON module_particpant.module_id=session.module_id INNER JOIN booking ON session.booking=booking.booking_id WHERE module_particpant.user_id = :id");
	$hoursData->bindParam(":id", $userId);
	$hoursData->execute();

	$hours = 0;
	while ($session = $hoursData->fetch(PDO::FETCH_ASSOC)) {
		$startTime = new DateTime($session["start_time"]);
		$endTime = new DateTime($session["end_time"]);
		$diff = $startTime->diff($endTime);
		$hours = $hours + $diff->h;
	}
?>

<!-- main heading -->
<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
    <h1 class="display-4">Select Tutor Modules</h1>
    <p class="lead">This system allows management of courses, modules, students etc.</p>

	<?php
		if(isset($_POST["moduleForm"])) {
			$failed = false;

			$addedModule = $_POST["moduleSelect"];
			$day = $_POST["daySelect"];
			$start = $_POST["startInput"];
			$end = $_POST["endInput"];
			$room = $_POST["locationSelect"];

			$timeStart = new DateTime($start);
			$timeEnd = new DateTime($end);
			
			if ($timeStart->format("h") > $timeEnd->format("h")) {
				$failed = true;
			} else if ($timeStart->format("h") == $timeEnd->format("h")) {
				if ($timeStart->format("i") > $timeEnd->format("i")) {
					$failed = true;
				}
			} else {
				$timeDiff = $timeStart->diff($timeEnd);
				$newHours = $hours + $timeDiff->h;
				if ($newHours > 20) {
					$failed = true;
				}
			}

			if (!$failed) {
				try {
					$addModuleSQL = "INSERT INTO module_particpant (`user_id`, `module_id`, `role`) VALUES (:id, :module, 2)";
					$addBookingSQL = "INSERT INTO booking (`room_id`, `day`, `start_time`, `end_time`) VALUES (:room, :day, :start, :end)";
					$addSessionSQL = "INSERT INTO session (`module_id`, `booking`) VALUES (:module, :booking)";

					$db->beginTransaction();

					$addModule = $db->prepare($addModuleSQL);
					$addModule->bindParam(":id", $userId);
					$addModule->bindParam(":module", $addedModule);
					$addModuleSuccess = $addModule->execute();

					if ($addModuleSuccess) {
						$addBooking = $db->prepare($addBookingSQL);
						$addBooking->bindParam(":room", $room);
						$addBooking->bindParam(":day", $day);
						$addBooking->bindParam(":start", $start);
						$addBooking->bindParam(":end", $end);
						$addBookingSuccess = $addBooking->execute();

						if ($addBookingSuccess) {
							$booking = $db->lastInsertId();
							$addSession = $db->prepare($addSessionSQL);
							$addSession->bindParam(":module", $addedModule);
							$addSession->bindParam(":booking", $booking);
							$addSessionSuccess = $addSession->execute();

							if (!$addBookingSuccess) {
								$failed = true;
							}
						} else {
							$failed = true;
						}
					} else {
						$failed = true;
					}
				} catch (Exception $error) {
					$failed = true;
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to add Module.</div>";
				}

				if ($failed) {
					$db->rollBack();
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to add Module.</div>";
				} else {
					$db->commit();
					echo "<script>$('.updateModuleForm')[0].reset();</script>";
					echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Module added!</div>";
				}
			} else {
				echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to add Module.</div>";
			}
		}
	?>
</div>

<div class="container">
	<div class="card-deck mb-3 text-left">
		<div class="card mb-4 box-shadow">
			<div class="card-header">
				<h4 class="my-0 font-weight-normal">Select Modules</h4>
			</div>
			<div class="card-body">
				<table class="table stripped">
					<?php
						try {
							$moduleData = $db->prepare("SELECT module_particpant.module_id, module.module_name, booking.start_time, booking.end_time FROM module_particpant INNER JOIN module ON module_particpant.module_id=module.module_id INNER JOIN session ON module.module_id=session.module_id INNER JOIN booking ON session.booking=booking.booking_id WHERE module_particpant.user_id = :id");
							$moduleData->bindParam(":id", $userId);
							$moduleData->execute();
						} catch (Exception $error) {
							echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to fetch Tutor Data.</div>";
						}
					?>
					<tr>
						<td>Modules</td>
						<td>
							<?php
								$moduleNames = [];
								if ($moduleData->rowCount() <= 0) {
									echo "None";
								} else {
									while ($module = $moduleData->fetch(PDO::FETCH_ASSOC)) {
										$moduleNames[] = $module["module_name"];
										echo $module["module_name"] . "<br>";
									}
								}
							?>
						</td>
						<td>
							<button id="updateModule" type="button" class="btn btn-info btn-sm float-right" data-toggle="modal" data-target="#updateModuleModal">Update Record</button>
						</td>
					</tr>
					<tr>
						<td>Current Hours per Week</td>
						<td>
							<?php
								echo $hours;
							?>
						</td>
						<td></td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<!-- Modal: Update Modules -->
	<div class="modal" id="updateModuleModal" tabindex="-1" role="dialog" aria-labelledby="updateModuleModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="updateNameLabel">Add Module</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="updateModuleForm" class="updateModuleForm" action="?id=<?php echo $userId ?>" method="post">
						<div class="form-group">
							<label for="moduleSelect">Module</label>
							<select class="custom-select" name="moduleSelect" required>
								<?php
									try {
										$tutorDepartments = $db->prepare("SELECT dept_id FROM tutor_department WHERE user_id = :id");
										$tutorDepartments->bindParam(":id", $userId);
										$tutorDepartments->execute();
										$deptIds = [];
										while ($dept = $tutorDepartments->fetch(PDO::FETCH_ASSOC)) {
											$deptIds[] = $dept["dept_id"];
										}

										$allModules = $db->prepare("SELECT course_module.module_id, module.module_name FROM department INNER JOIN course ON department.dept_id=course.dept INNER JOIN course_module ON course.course_id=course_module.course_id INNER JOIN module ON course_module.module_id=module.module_id WHERE department.dept_id IN (" . implode(",", $deptIds) . ")");
										$allModules->execute();

										if ($allModules->rowCount() <= 0) {
											echo "<option value='' disabled selected>None Available</option>";
										} else {
											while ($module = $allModules->fetch(PDO::FETCH_ASSOC)) {
												if (!in_array($module["module_name"], $moduleNames)) {
													echo "<option value='" . $module["module_id"] . "'>" . $module["module_name"] . "</option>";
												}
											}
										}
									} catch (Exception $error) {
										echo $error;
									}
								?>
							</select>
						</div>
						<div class="form-group">
							<label for="daySelect">Day of Week</label>
							<select class="custom-select" name="daySelect" required>
								<option value="1">Monday</option>
								<option value="2">Tuesday</option>
								<option value="3">Wednesday</option>
								<option value="4">Thursday</option>
								<option value="5">Friday</option>
								<option value="6">Saturday</option>
								<option value="7">Sunday</option>
							</select>
						</div>
						<div class="form-group">
							<label for="startInput">Start Time</label>
							<input type="time" class="form-control" name="startInput" required min="08:00" max="16:00">
						</div>
						<div class="form-group">
							<label for="endInput">End Time</label>
							<input type="time" class="form-control" name="endInput" required min="09:00" max="17:00">
						</div>
						<div class="form-group">
							<label for="locationSelect">Location</label>
							<select class="custom-select" name="locationSelect" required>
								<?php
									$rooms = $db->query("SELECT room.room_id, room.room_number, room.room_name, building.building_name FROM room INNER JOIN building ON room.building_id=building.building_id");
									while ($room = $rooms->fetch(PDO::FETCH_ASSOC)) {
										echo "<option value='" . $room["room_id"] . "'>" . $room["building_name"] . " | " . $room["room_number"] . " " . $room["room_name"] . "</option>";
									}
								?>
							</select>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" name="moduleForm" form="updateModuleForm" class="btn btn-primary" onclick="return confirm('Are you sure?')" value="Save changes">
				</div>
			</div>
		</div>
	</div>

<?php 
    // include the footer file
    require_once("footer-inc.php"); 
?>