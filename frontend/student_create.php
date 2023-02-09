<?php 
	// include database connection script
	require_once("db-connect-inc.php");
	// include the header file
	require_once("header-inc.php");
	// Update title
	$pagetitle = "Create Student";
?>

<!-- main heading -->
<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
    <h1 class="display-4">Create a Student</h1>
    <p class="lead">This system allows management of courses, modules, students etc.</p>
	<?php
		// form submit handling
		if(isset($_POST["formSubmit"])){ // has the form been submitted
			// get all fields of data from form
			$firstName = $_POST["firstNameInput"];
			$lastName = $_POST["lastNameInput"];
			$address = $_POST["addressInput"];
			$city = $_POST["cityInput"];
			$county = $_POST["countyInput"];
			$postcode = $_POST["postcodeInput"];
			$course = $_POST["courseInput"];

			// validation
			if (strlen($firstName) < 50 || strlen($lastName) < 50 || strlen($address) < 75 || strlen($city) < 50 || strlen($county) < 100 || strlen($postcode) < 10) {
				$email = $firstName . "." . $lastName . "" . date("d") . "@uni.ac.uk"; // create email
				
				$addressInsert = $db->prepare("INSERT INTO address (address, city, county, postcode) VALUES (:address, :city, :county, :postcode)");
				$addressInsert->bindParam(":address", $address);
				$addressInsert->bindParam(":city", $city);
				$addressInsert->bindParam(":county", $county);
				$addressInsert->bindParam(":postcode", $postcode);
				$addressSuccess = $addressInsert->execute();
				$addressId = $db->lastInsertId();

				if ($addressSuccess == true) {
					$studentSQL = "INSERT INTO student (course, year, semester) VALUES (:course, 1, 1)";
					$userSQL = "INSERT INTO user (student_number, first_name, last_name, email, address_id, role) VALUES (:student_number, :first, :last, :email, :address_id, 1)";

					$studentInsert = $db->prepare($studentSQL);
					$studentInsert->bindParam(":course", $course);

					$userInsert = $db->prepare($userSQL);
					$userInsert->bindParam(":first", $firstName);
					$userInsert->bindParam(":last", $lastName);
					$userInsert->bindParam(":email", $email);
					$userInsert->bindParam(":address_id", $addressId);

					$db->beginTransaction();
					$studentInsertSuccess = $studentInsert->execute();

					if ($studentInsertSuccess == false) {
						$db->rollBack();
						echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Please check all fields are entered correctly.</div>";
					} else {
						$student_number = $db->lastInsertId();
						$userInsert->bindParam(":student_number", $student_number);
						$userInsertSuccess = $userInsert->execute();

						if ($userInsertSuccess == false) {
							$db->rollBack();
							echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Please check all fields are entered correctly.</div>";
						} else {
							$db->commit();
							
							echo "<script>$('.studentForm')[0].reset();</script>";
							echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Student has been created!</div>";
						}
					}
				} else {
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
                <h4 class="my-0 font-weight-normal">Create Student</h4>
            </div>

			<div class="card-body">
				<form class="studentForm" action="" method="post">
					<div class="form-group">
						<label for="firstNameInput">First Name</label>
						<input type="text" class="form-control" name="firstNameInput" placeholder="Sharon" required minlength="1" maxlength="50">
					</div>
					<div class="form-group">
						<label for="lastNameInput">Last Name</label>
						<input type="text" class="form-control" name="lastNameInput" placeholder="Smith" required minlength="1" maxlength="50">
					</div>
					<div class="form-group">
						<label for="addressInput">Address</label>
						<input type="text" class="form-control" name="addressInput" placeholder="3523 Tristique Rd." required minlength="1" maxlength="75">
					</div>
					<div class="form-group">
						<label for="cityInput">City</label>
						<input type="text" class="form-control" name="cityInput" placeholder="Long Eaton" required minlength="1" maxlength="50">
					</div>
					<div class="form-group">
						<label for="countyInput">County</label>
						<input type="text" class="form-control" name="countyInput" placeholder="Durham" required minlength="1" maxlength="100">
					</div>
					<div class="form-group">
						<label for="postcodeInput">Postal Code</label>
						<input type="text" class="form-control" name="postcodeInput" placeholder="QP8 5ST" required minlength="1" maxlength="10">
					</div>
					<div class="form-group">
						<label for="courseInput">Course</label>
						<select name="courseInput" class="custom-select" required>
							<?php
								$courses = $db->query("SELECT course_id, course_name FROM course");
								while ($course = $courses->fetch(PDO::FETCH_ASSOC)) {
									echo "<option value='" . $course["course_id"] . "'>" . $course["course_name"] . "</option>";
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