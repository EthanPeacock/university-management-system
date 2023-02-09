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
    <h1 class="display-4">Select Student Modules</h1>
    <p class="lead">This system allows management of courses, modules, students etc.</p>

	<?php
		if(isset($_POST["moduleForm"])) {
			$addedModule = $_POST["moduleSelect"];

			try {
				$addModule = $db->prepare("INSERT INTO module_particpant (`user_id`, `module_id`, `role`) VALUES (:id, :module, 1)");
				$addModule->bindParam(":id", $userId);
				$addModule->bindParam(":module", $addedModule);
				$addModule->execute();

				echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Module has been added!</div>";
				echo "<button type='button' class='btn btn-outline-info btn-lg'><a class='text-dark' href=''>Refresh to see any changes</a></button>";
			} catch (Exception $error) {
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
							$studentData = $db->prepare("SELECT user.student_number, student.year, student.semester, course.course_id, course.course_name FROM user INNER JOIN student ON user.student_number=student.student_number INNER JOIN course ON student.course=course.course_id WHERE user.user_id = :id");
							$studentData->bindParam(":id", $userId);
							$studentData->execute();
							$studentDataRow = $studentData->fetch(PDO::FETCH_ASSOC);

							$moduleData = $db->prepare("SELECT module_particpant.module_id, module.module_name FROM module_particpant INNER JOIN module ON module_particpant.module_id=module.module_id WHERE module_particpant.user_id = :id");
							$moduleData->bindParam(":id", $userId);
							$moduleData->execute();
						} catch (Exception $error) {
							echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to fetch Student Data.</div>";
						}
					?>
					<tr>
						<td>Course</td>
						<td><?php echo $studentDataRow["course_name"] ?></td>
						<td></td>
					</tr>
					<tr>
						<td>Current Year</td>
						<td><?php echo $studentDataRow["year"] ?></td>
						<td></td>
					</tr>
					<tr>
						<td>Current Semester</td>
						<td><?php echo $studentDataRow["semester"] ?></td>
						<td></td>
					</tr>
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
							<label for="moduleInput">Module</label>
							<select class="custom-select" name="moduleSelect" required>
								<?php
									$allModules = $db->prepare("SELECT course_module.module_id, module.module_name FROM course_module INNER JOIN module ON course_module.module_id=module.module_id WHERE course_module.course_id = :id AND course_module.year = :y AND course_module.semester = :sem");
									$allModules->bindParam(":id", $studentDataRow["course_id"]);
									$allModules->bindParam(":y", $studentDataRow["year"]);
									$allModules->bindParam(":sem", $studentDataRow["semester"]);
									$allModules->execute();

									if ($allModules->rowCount() <= 0) {
										echo "<option value='' disabled selected>None Available</option>";
									} else {
										while ($module = $allModules->fetch(PDO::FETCH_ASSOC)) {
											if (!in_array($module["module_name"], $moduleNames)) {
												echo "<option value='" . $module["module_id"] . "'>" . $module["module_name"] . "</option>";
											} else {
												echo "<option value='' disabled selected>None Available</option>";
											}
										}
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