<?php 
	require_once("db-connect-inc.php");
	$courseId = $_GET["id"];
	$failed = false;

	try {
		$courseData = $db->prepare("SELECT course.course_id, course.course_name, course.dept, course.length, department.dept_name FROM course INNER JOIN department ON course.dept = department.dept_id WHERE course.course_id = :course_id");
		$courseData->bindParam(":course_id", $courseId);
		$courseData->execute();
		$courseCount = $courseData->rowCount();
		$courseRow = $courseData->fetch(PDO::FETCH_ASSOC);

		$moduleData = $db->prepare("SELECT course_module.module_id, course_module.year, course_module.semester, module.module_name, module.module_code FROM course_module INNER JOIN module ON course_module.module_id = module.module_id WHERE course_module.course_id = :id ORDER BY course_module.year, course_module.semester");
		$moduleData->bindParam(":id", $courseId);
		$moduleData->execute();
		$moduleCount = $moduleData->rowCount();

		if ($courseCount <= 0 || $moduleCount <= 0) {
			$failed = true;
			$pagetitle = "Invalid Course";
		} else {
			$pagetitle = "Course Details: " . $courseRow["course_name"];
		}
	} catch (Exception $error) {
		echo $error;
		$failed = true;
	}

	require_once('header-inc.php'); 
?>

<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
	<h1 class="display-4">View Course</h1>
	<p class="lead">This system allows management of courses, modules, students etc.</p>

	<?php
		if(isset($_POST["nameForm"])){ // has the form been submitted
			// get all fields of data from form
			$name = $_POST["nameInput"];

			if (strlen($name) != 0) {
				if (strlen($name) <= 100) {
					try {
						$updateName = $db->prepare("UPDATE course SET course_name = :newname WHERE course_id = :id");
						$updateName->bindParam(":newname", $name);
						$updateName->bindParam(":id", $courseId);
						$updateName->execute();

						echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Course Name has been updated!</div>";
					} catch (Exception $error) {
						echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Course Name.</div>";
					}
				} else {
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Course Name.</div>";
				}
			}

			echo "<button type='button' class='btn btn-outline-info btn-lg'><a class='text-dark' href=''>Refresh to see any changes</a></button>";
			echo "<script>$('.updateNameForm')[0].reset();</script>"; // reset form
		}

		if(isset($_POST["lengthForm"])){ // has the form been submitted
			// get all fields of data from form
			$length = $_POST["lengthInput"];

			if ($length >= 1 || $length <= 6) {
				try {
					$updateLength = $db->prepare("UPDATE course SET length = :newlen WHERE course_id = :id");
					$updateLength->bindParam(":newlen", $length);
					$updateLength->bindParam(":id", $courseId);
					$updateLength->execute();

					echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Course Length has been updated!</div>";
				} catch (Exception $error) {
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Course Length.</div>";
				}
			} else {
				echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Course Length.</div>";
			}

			echo "<button type='button' class='btn btn-outline-info btn-lg'><a class='text-dark' href=''>Refresh to see any changes</a></button>";
			echo "<script>$('.updateLengthForm')[0].reset();</script>"; // reset form
		}

		if (isset($_POST["departmentForm"])) { // has the form been submitted
			$newDept = $_POST["dept"];

			try {
				$updateDepartment = $db->prepare("UPDATE course SET dept = :newdept WHERE course_id = :id");
				$updateDepartment->bindParam(":newdept", $newDept);
				$updateDepartment->bindParam(":id", $courseId);
				$updateDepartment->execute();

				echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Course Department has been updated!</div>";
			} catch (Exception $error) {
				echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Course Department.</div>";
			}

			echo "<button type='button' class='btn btn-outline-info btn-lg'><a class='text-dark' href=''>Refresh to see any changes</a></button>";
			echo "<script>$('.updateDepartmentForm')[0].reset();</script>"; // reset form
		}

		if (isset($_POST["moduleForm"])) { // has the form been submitted
			$module = $_POST["moduleSelect"];
			$year = $_POST["yearInput"];
			$semester = $_POST["semesterInput"];

			try {
				$courseModules = $db->prepare("SELECT course_id FROM course_module WHERE course_id = :id");
				$courseModules->bindParam(":id", $courseId);
				$courseModules->execute();
				$courseModulesCount = $courseModules->rowCount();

				if ($courseModulesCount < 20) {
					$courseModuleInsert = $db->prepare("INSERT INTO course_module (`course_id`, `module_id`, `year`, `semester`) VALUES (:course, :module, :year, :semester)");
					$courseModuleInsert->bindParam(":course", $courseId);
					$courseModuleInsert->bindParam(":module", $module);
					$courseModuleInsert->bindParam(":year", $year);
					$courseModuleInsert->bindParam(":semester", $semester);
					$courseModuleInsert->execute();

					echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Course Module has been added!</div>";
				} else {
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to add Course Module - 20 Max.</div>";
				}
			} catch (Exception $error) {
				echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to add Course Module.</div>";
			}

			echo "<button type='button' class='btn btn-outline-info btn-lg'><a class='text-dark' href=''>Refresh to see any changes</a></button>";
			echo "<script>$('.updateModuleForm')[0].reset();</script>"; // reset form
		}

		if (isset($_POST["moduleDeleteForm"])) { // has the form been submitted
			if (count($_POST) <= 1) {
				echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to delete Course Modules.</div>";
			} else {
				$failed = false;

				foreach ($_POST as $module) {
					if ($module != "Save changes") {
						try {
							$moduleRemove = $db->prepare("DELETE FROM course_module WHERE course_id = :courseid AND module_id = :moduleid");
							$moduleRemove->bindParam(":courseid", $courseId);
							$moduleRemove->bindParam(":moduleid", $module);
							$moduleRemoveSuccess = $moduleRemove->execute();

							if (!$moduleRemoveSuccess) {
								$failed = true;
								break;
							}
						} catch (Exception $error) {
							$failed = true;
							break;
						}
					}
				}

				if (!$failed) {
					echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Course Modules have been updated!</div>";
					echo "<button type='button' class='btn btn-outline-info btn-lg'><a class='text-dark' href=''>Refresh to see any changes</a></button>";
				} else {
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Course Modules.</div>";
				}
			}
		}
	?>
</div>

<div class="container">
    <div class="card-deck mb-3 text-left">
        <div class="card mb-4 box-shadow">
            <div class="card-header">
                <h4 class="my-0 font-weight-normal">Course details</h4>
            </div>
            <div class="card-body">
				<?php
					if ($error == true) {
						echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> Course doesn't exist.</div>";
					}
				?>
				<table class="table striped">
					<?php
						if ($error == false) {
							$moduleNames = [];

							echo "<tr><td>";
							echo "Course Name";
							echo "</td><td>";
							echo $courseRow["course_name"];
							echo "</td><td>";
							echo "<button id='updateName' type='button' class='btn btn-info btn-sm' data-toggle='modal' data-target='#updateNameModal'>Update Record</button>";
							echo "</td></tr>";

							echo "<tr><td>";
							echo "Department";
							echo "</td><td>";
							echo $courseRow["dept_name"];
							echo "</td><td>";
							echo "<button id='updateDepartment' type='button' class='btn btn-info btn-sm' data-toggle='modal' data-target='#updateDepartmentModal'>Update Record</button>";
							echo "</td></tr>";

							echo "<tr><td>";
							echo "Length (Years)";
							echo "</td><td>";
							echo $courseRow["length"];
							echo "</td><td>";
							echo "<button id='updateLength' type='button' class='btn btn-info btn-sm' data-toggle='modal' data-target='#updateLengthModal'>Update Record</button>";
							echo "</td></tr>";

							echo "<tr><td>";
							echo "Modules";
							echo "</td><td>";
							echo "<table class='table stripped'>";
							while ($module = $moduleData->fetch(PDO::FETCH_ASSOC)) {
								$moduleNames[] = $module["module_name"];
								echo "<tr><td>";
								echo $module["module_name"] . " (" . $module["module_code"] . ")";
								echo "</td><td>";
								echo "Yr: " . $module["year"];
								echo "</td><td>";
								echo "Sem: " . $module["semester"];
								echo "</td></tr>";
							}
							echo "</table></td><td>";
							echo "<button id='updateModule' type='button' class='btn btn-info btn-sm' data-toggle='modal' data-target='#updateModuleModal'>Update Record</button>";
							echo "<button id='deleteModule' type='button' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#deleteModuleModal'>Delete Record</button>";
							echo "</td></tr>";
						}
					?>
				</table>
                <button type="button" class="btn btn-lg btn-block btn-outline-primary">
					<a href="courses.php?page=1">See all courses</a>
				</button>
            </div>
        </div>
    </div> <!-- end card-deck -->

	<!-- Modal: Update Name -->
	<div class="modal" id="updateNameModal" tabindex="-1" role="dialog" aria-labelledby="updateNameModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="updateNameLabel">Update Course Name</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="updateNameForm" class="updateNameForm" action="?id=<?php echo $courseId ?>" method="post">
						<div class="form-group">
							<label for="nameInput">Name</label>
							<input type="text" class="form-control" name="nameInput" minlength="1" maxlength="100">
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" name="nameForm" form="updateNameForm" class="btn btn-primary" onclick="return confirm('Are you sure?')" value="Save changes">
				</div>
			</div>
		</div>
	</div>

	<!-- Modal: Update Length -->
	<div class="modal" id="updateLengthModal" tabindex="-1" role="dialog" aria-labelledby="updateLengthModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="updateNameLabel">Update Course Length</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="updateLengthForm" class="updateLengthForm" action="?id=<?php echo $courseId ?>" method="post">
						<div class="form-group">
							<label for="nameInput">Length (Years)</label>
							<?php
								echo "<input type='range' class='lengthInput' min='1' max='6' step='1' id='lengthInput' name='lengthInput' value='" . $courseRow["length"] . "'>";
							?>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" name="lengthForm" form="updateLengthForm" class="btn btn-primary" onclick="return confirm('Are you sure?')" value="Save changes">
				</div>
			</div>
		</div>
	</div>

	<!-- Modal: Update Department -->
	<div class="modal" id="updateDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="updateDepartmentModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="updateNameLabel">Update Course Department</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="updateDepartmentForm" class="updateDepartmentForm" action="?id=<?php echo $courseId ?>" method="post">
						<?php
							$allDepartments = $db->query("SELECT * FROM department");
							while ($dept = $allDepartments->fetch(PDO::FETCH_ASSOC)) {
								$deptName = $dept["dept_name"];
								$deptId = $dept["dept_id"];

								echo "<div class='input-group'><div class='input-group-prepend'><div class='input-group-text'>";
								if ($deptName == $courseRow["dept_name"]) {
									echo "<input type='radio' aria-label='Radio Button' checked name='dept' value='" . $deptId . "'>";
								} else {
									echo "<input type='radio' aria-label='Radio Button' name='dept' value='" . $deptId . "'>";
								}
								echo "</div></div><input type='text' class='form-control bg-white' aria-label='Department Name' disabled value='" . $deptName . " - " . $deptId . "'></div>";
							}
						?>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" name="departmentForm" form="updateDepartmentForm" class="btn btn-primary" onclick="return confirm('Are you sure?')" value="Save changes">
				</div>
			</div>
		</div>
	</div>

	<!-- Modal: Update Modules -->
	<div class="modal" id="updateModuleModal" tabindex="-1" role="dialog" aria-labelledby="updateModuleModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="updateNameLabel">Update Course Modules</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="updateModuleForm" class="updateModuleForm" action="?id=<?php echo $courseId ?>" method="post">
						<div class="form-group">
							<label for="moduleInput">Module</label>
							<select class="custom-select" name="moduleSelect" required>
								<?php
									$allModules = $db->query("SELECT module_id, module_name FROM module");
									while ($module = $allModules->fetch(PDO::FETCH_ASSOC)) {
										if (!in_array($module["module_name"], $moduleNames)) {
											echo "<option value='" . $module["module_id"] . "'>" . $module["module_name"] . "</option>";
										}
									}
								?>
							</select>
						</div>
						<div class="form-group">
							<label for="yearInput">Year</label>
							<?php
								echo "<input type='range' class='yearInput' min='1' max='" . $courseRow["length"] . "' step='1' id='yearInput' name='yearInput' value='1'>";
							?>
						</div>
						<div class="form-group">
							<label for="semesterInput">Semester</label>
							<input type="range" class="semesterInput" min="1" max="2" step="1" id="semesterInput" name="semesterInput" value="1">
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

	<!-- Modal: Delete Modules -->
	<div class="modal" id="deleteModuleModal" tabindex="-1" role="dialog" aria-labelledby="deleteModuleModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="updateNameLabel">Delete Course Module(s)</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="deleteModuleForm" class="deleteModuleForm" action="?id=<?php echo $courseId ?>" method="post">
						<?php
							$courseModules = $db->prepare("SELECT course_module.module_id, module.module_name FROM course_module INNER JOIN module ON course_module.module_id = module.module_id WHERE course_module.course_id = :id");
							$courseModules->bindParam(":id", $courseId);
							$courseModules->execute();

							while ($module = $courseModules->fetch(PDO::FETCH_ASSOC)) {
								$moduleName = $module["module_name"];
								$moduleId = $module["module_id"];

								echo "<div class='input-group'><div class='input-group-prepend'><div class='input-group-text'>";
								echo "<input type='checkbox' aria-label='Checkbox' name=" . $moduleName . " value='" . $moduleId . "'>";
								echo "</div></div><input type='text' class='form-control bg-white' aria-label='Department Name' disabled value='" . $moduleName . " - " . $moduleId . "'></div>";
							}
						?>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" name="moduleDeleteForm" form="deleteModuleForm" class="btn btn-primary" onclick="return confirm('Are you sure?')" value="Save changes">
				</div>
			</div>
		</div>
	</div>

<?php 
	require_once("footer-inc.php"); 
?>