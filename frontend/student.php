<?php 
	require_once("db-connect-inc.php");
	$user_id = $_GET["id"];
	$error = false;

	try {
		$user_data = $db->prepare("SELECT * FROM user WHERE user_id = :id");
		$user_data->bindParam(":id", $user_id);
		$user_data->execute();
		$user_count = $user_data->rowCount();
		$user_row = $user_data->fetch(PDO::FETCH_ASSOC);

		$student_data = $db->prepare("SELECT * FROM student WHERE student_number = :student_number");
		$student_data->bindParam(":student_number", $user_row["student_number"]);
		$student_data->execute();
		$student_count = $student_data->rowCount();
		$student_row = $student_data->fetch(PDO::FETCH_ASSOC);

		$address_data = $db->prepare("SELECT * FROM address WHERE address_id = :address");
		$address_data->bindParam(":address", $user_row["address_id"]);
		$address_data->execute();
		$address_count = $address_data->rowCount();
		$address_row = $address_data->fetch(PDO::FETCH_ASSOC);

		$course_data = $db->prepare("SELECT * FROM course WHERE course_id = :course_id");
		$course_data->bindParam(":course_id", $student_row["course"]);
		$course_data->execute();
		$course_count = $course_data->rowCount();
		$course_row = $course_data->fetch(PDO::FETCH_ASSOC);

		if ($user_count <= 0 || $student_count <= 0 || $address_count <= 0 || $course_count <= 0) {
			$error = true;
			$pagetitle = "Invalid Student";
		} else {
			$pagetitle = "Student Details: " . $user_row["first_name"] . " " . $user_row["last_name"];
		}
	} catch (Exception $error) {
		$error = true;
	}

	require_once('header-inc.php'); 
?>

<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
	<h1 class="display-4">View Students</h1>
	<p class="lead">This system allows management of courses, modules, students etc.</p>

	<?php
		if(isset($_POST["nameForm"])){ // has the form been submitted
			// get all fields of data from form
			$firstName = $_POST["firstNameInput"];
			$lastName = $_POST["lastNameInput"];

			if (strlen($firstName) != 0) {
				if (strlen($firstName) < 50) {
					try {
						$updateFirstName = $db->prepare("UPDATE user SET first_name = :newname WHERE user_id = :id");
						$updateFirstName->bindParam(":newname", $firstName);
						$updateFirstName->bindParam(":id", $user_id);
						$updateFirstName->execute();

						echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Student First Name has been updated!</div>";
					} catch (Exception $error) {
						echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Student Name.</div>";
					}
				} else {
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Student Name.</div>";
				}
			}

			if (strlen($lastName) != 0) {
				if (strlen($firstName) < 50) {
					try {
						$updateLastName = $db->prepare("UPDATE user SET last_name = :newname WHERE user_id = :id");
						$updateLastName->bindParam(":newname", $lastName);
						$updateLastName->bindParam(":id", $user_id);
						$updateLastName->execute();

						echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Student Last Name has been updated!</div>";
					} catch (Exception $error) {
						echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Student Name.</div>";
					}
				} else {
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Student Name.</div>";
				}
			}

			echo "<button type='button' class='btn btn-outline-info btn-lg'><a class='text-dark' href=''>Refresh to see any changes</a></button>";
			echo "<script>$('.updateNameForm')[0].reset();</script>"; // reset form
		}

		if(isset($_POST["addressForm"])){ // has the form been submitted
			$address = $_POST["addressInput"];
			$city = $_POST["cityInput"];
			$county = $_POST["countyInput"];
			$postcode = $_POST["postcodeInput"];

			try {
				$addressQuery = $db->prepare("SELECT address_id FROM user WHERE user_id = :id");
				$addressQuery->bindParam(":id", $user_id);
				$addressQuery->execute();
				if ($addressQuery->rowCount() < 1) {
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Student Address.</div>";
				} else {
					$addressData = $addressQuery->fetch(PDO::FETCH_ASSOC);
					$addressId = $addressData["address_id"];
				}
			} catch (Exception $error) {
				echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Student Address.</div>";
			}

			if (strlen($address) != 0) {
				if (strlen($address) < 75) {
					try {
						$updateAddress = $db->prepare("UPDATE address SET address = :newAddress WHERE address_id = :id");
						$updateAddress->bindParam(":newAddress", $address);
						$updateAddress->bindParam(":id", $addressId);
						$updateAddress->execute();

						echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Student Address has been updated!</div>";
					} catch (Exception $error) {
						echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Student Address.</div>";
					}
				} else {
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Student Address.</div>";
				}
			}

			if (strlen($city) != 0) {
				if (strlen($city) < 50) {
					try {
						$updateAddress = $db->prepare("UPDATE address SET city = :newCity WHERE address_id = :id");
						$updateAddress->bindParam(":newCity", $city);
						$updateAddress->bindParam(":id", $addressId);
						$updateAddress->execute();

						echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Student City has been updated!</div>";
					} catch (Exception $error) {
						echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Student Address.</div>";
					}
				} else {
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Student Address.</div>";
				}
			}

			if (strlen($county) != 0) {
				if (strlen($county) < 100) {
					try {
						$updateAddress = $db->prepare("UPDATE address SET county = :newCounty WHERE address_id = :id");
						$updateAddress->bindParam(":newCounty", $county);
						$updateAddress->bindParam(":id", $addressId);
						$updateAddress->execute();

						echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Student County has been updated!</div>";
					} catch (Exception $error) {
						echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Student Address.</div>";
					}
				} else {
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Student Address.</div>";
				}
			}

			if (strlen($postcode) != 0) {
				if (strlen($postcode) < 10) {
					try {
						$updateAddress = $db->prepare("UPDATE address SET postcode = :newPostcode WHERE address_id = :id");
						$updateAddress->bindParam(":newPostcode", $postcode);
						$updateAddress->bindParam(":id", $addressId);
						$updateAddress->execute();

						echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Student Postal Code has been updated!</div>";
					} catch (Exception $error) {
						echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Student Address.</div>";
					}
				} else {
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Student Address.</div>";
				}
			}

			echo "<button type='button' class='btn btn-outline-info btn-lg'><a class='text-dark' href=''>Refresh to see any changes</a></button>";
			echo "<script>$('.updateAddressForm')[0].reset();</script>"; // reset form
		}
	?>
</div>

<div class="container">
    <div class="card-deck mb-3 text-left">
        <div class="card mb-4 box-shadow">
            <div class="card-header">
                <h4 class="my-0 font-weight-normal float-left">Student details</h4>
				<button class="btn btn-sm btn-primary float-right">
					<a href="timetable.php?id=<?php echo $user_id ?>" class="text-white">View Timetable</a>
				</button>
				<button class="btn btn-sm btn-primary float-right mr-2">
					<a href="student_modules.php?id=<?php echo $user_id ?>" class="text-white">Select Modules</a>
				</button>
            </div>
            <div class="card-body">
				<?php
					if ($error == true) {
						echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> Student doesn't exist.</div>";
					}
				?>
				<table class="table striped">
					<?php
						if ($error == false) {
							echo "<tr><td>";
							echo "Full Name";
							echo "</td><td>";
							echo $user_row["first_name"] . " " . $user_row["last_name"];
							echo "</td><td>";
							echo "<button id='updateName' type='button' class='btn btn-info btn-sm' data-toggle='modal' data-target='#updateNameModal'>Update Record</button>";
							echo "</td></tr>";

							echo "<tr><td>";
							echo "Email";
							echo "</td><td>";
							echo $user_row["email"];
							echo "</td></tr>";

							echo "<tr><td>";
							echo "Address";
							echo "</td><td>";
							echo $address_row["address"] . "<br>" . $address_row["city"] . "<br>" . $address_row["county"] . "<br>" . $address_row["postcode"];
							echo "</td><td>";
							echo "<button id='updateAddress' type='button' class='btn btn-info btn-sm' data-toggle='modal' data-target='#updateAddressModal'>Update Record</button>";
							echo "</td></tr>";

							echo "<tr><td>";
							echo "Student Number";
							echo "</td><td>";
							echo $user_row["student_number"];
							echo "</td></tr>";

							echo "<tr><td>";
							echo "Course";
							echo "</td><td>";
							echo $course_row["course_name"];
							echo "</td></tr>";

							echo "<tr><td>";
							echo "Year of Study";
							echo "</td><td>";
							echo $student_row["year"];
							echo "</td></tr>";
						}
					?>
				</table>
                <button type="button" class="btn btn-lg btn-block btn-outline-primary">
					<a href="students.php">See all students</a>
				</button>
            </div>
        </div>
    </div> <!-- end card-deck -->

	<!-- Modal: Update Name -->
	<div class="modal" id="updateNameModal" tabindex="-1" role="dialog" aria-labelledby="updateNameModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="updateNameLabel">Update Students Name</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="updateNameForm" class="updateNameForm" action="?id=<?php echo $user_id ?>" method="post">
						<div class="form-group">
							<label for="firstNameInput">First Name</label>
							<input type="text" class="form-control" name="firstNameInput" placeholder="Sharon" minlength="1" maxlength="50">
						</div>
						<div class="form-group">
							<label for="lastNameInput">Last Name</label>
							<input type="text" class="form-control" name="lastNameInput" placeholder="Smith" minlength="1" maxlength="50">
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

	<!-- Modal: Update Address -->
	<div class="modal" id="updateAddressModal" tabindex="-1" role="dialog" aria-labelledby="updateAddressModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="updateAddressLabel">Update Students Address</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="updateAddressForm" class="updateAddressForm" action="?id=<?php echo $user_id ?>" method="post">
						<div class="form-group">
							<label for="addressInput">Address</label>
							<input type="text" class="form-control" name="addressInput" placeholder="3523 Tristique Rd." minlength="1" maxlength="75">
						</div>
						<div class="form-group">
							<label for="cityInput">City</label>
							<input type="text" class="form-control" name="cityInput" placeholder="Long Eaton" minlength="1" maxlength="50">
						</div>
						<div class="form-group">
							<label for="countyInput">County</label>
							<input type="text" class="form-control" name="countyInput" placeholder="Durham" minlength="1" maxlength="100">
						</div>
						<div class="form-group">
							<label for="postcodeInput">Postal Code</label>
							<input type="text" class="form-control" name="postcodeInput" placeholder="QP8 5ST" minlength="1" maxlength="10">
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" name="addressForm" form="updateAddressForm" class="btn btn-primary" onclick="return confirm('Are you sure?')" value="Save changes">
				</div>
			</div>
		</div>
	</div>

<?php 
	require_once("footer-inc.php"); 
?>