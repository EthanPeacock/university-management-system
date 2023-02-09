<?php 
	// include database connection script
	require_once("db-connect-inc.php");
	// include the header file
	require_once("header-inc.php");
	// Update title
	$pagetitle = "Create Course";
?>

<!-- main heading -->
<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
    <h1 class="display-4">Create a Course</h1>
    <p class="lead">This system allows management of courses, modules, students etc.</p>
	<?php
		// form submit handling
		if(isset($_POST["formSubmit"])){ // has the form been submitted
			// get all fields of data from form
			$courseName = $_POST["courseNameInput"];
			$courseLength = $_POST["lengthInput"];
			$courseDept = $_POST["deptInput"];

			// validation
			if (strlen($courseName) <= 100 || $courseLength <= 10) {
				try {
					$courseCreate = $db->prepare("INSERT INTO course (`course_name`, `dept`, `length`) VALUES (:name, :dept, :len)");
					$courseCreate->bindParam(":name", $courseName);
					$courseCreate->bindParam(":dept", $courseDept);
					$courseCreate->bindParam(":len", $courseLength);
					$courseCreate->execute();

					echo "<script>$('.courseForm')[0].reset();</script>";
					echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Course has been created!</div>";
				} catch (Exception $error) {
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Please check all fields are entered correctly.</div>";
				}
			} else {
				echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Please check all fields are entered correctly.</div>";
			}
		}
	?>
</div>

<div class="container">
    <div class="card-deck mb-3 text-left">
        <!-- card one -->
        <div class="card mb-4 box-shadow">
            <div class="card-header">
                <h4 class="my-0 font-weight-normal">Create Tutor</h4>
            </div>

			<div class="card-body">
				<form class="courseForm" action="" method="post">
					<div class="form-group">
						<label for="courseNameInput">Course Name</label>
						<input type="text" class="form-control" name="courseNameInput" placeholder="BA (Hons) Acting" required minlength="1" maxlength="100">
					</div>
					<div class="form-group">
						<label for="lengthInput">Length</label>
						<input type="number" class="form-control" name="lengthInput" value="1" min="1" max="10" require>
					</div>
					<div class="form-group">
						<label for="deptInput">Department</label>
						<select name="deptInput" class="custom-select" required>
							<?php
								$departments = $db->query("SELECT dept_id, dept_name FROM department");
								while ($dept = $departments->fetch(PDO::FETCH_ASSOC)) {
									echo "<option value='" . $dept["dept_id"] . "'>" . $dept["dept_name"] . "</option>";
								}
							?>
						</select>
					</div>
					<input type="submit" class="btn btn-lg btn-primary btn-block" name="formSubmit">
				</form>
            </div>

        </div>
    </div> <!-- end card-deck -->

<?php 
    // include the footer file
    require_once("footer-inc.php"); 
?>