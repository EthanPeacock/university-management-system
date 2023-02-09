<?php 
	require_once("db-connect-inc.php");
	$moduleId = $_GET["id"];
	$failed = false;

	try {
		$moduleData = $db->prepare("SELECT module_name, module_code FROM module WHERE module_id = :id");
		$moduleData->bindParam(":id", $moduleId);
		$moduleData->execute();
		$moduleCount = $moduleData->rowCount();
		$moduleRow = $moduleData->fetch(PDO::FETCH_ASSOC);

		if ($moduleCount <= 0) {
			$failed = true;
			$pagetitle = "Invalid Module";
		} else {
			$pagetitle = "Module Details: " . $moduleRow["module_name"];
		}
	} catch (Exception $error) {
		echo $error;
		$failed = true;
	}

	require_once('header-inc.php'); 
?>

<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
	<h1 class="display-4">View Module</h1>
	<p class="lead">This system allows management of courses, modules, students etc.</p>

	<?php
		if(isset($_POST["nameForm"])){ // has the form been submitted
			// get all fields of data from form
			$name = $_POST["nameInput"];

			if (strlen($name) != 0) {
				if (strlen($name) <= 75) {
					try {
						$updateName = $db->prepare("UPDATE module SET module_name = :newname WHERE module_id = :id");
						$updateName->bindParam(":newname", $name);
						$updateName->bindParam(":id", $moduleId);
						$updateName->execute();

						echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Module Name has been updated!</div>";
					} catch (Exception $error) {
						echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Module Name.</div>";
					}
				} else {
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Module Name.</div>";
				}
			}

			echo "<button type='button' class='btn btn-outline-info btn-lg'><a class='text-dark' href=''>Refresh to see any changes</a></button>";
			echo "<script>$('.updateNameForm')[0].reset();</script>"; // reset form
		}

		if(isset($_POST["codeForm"])){ // has the form been submitted
			// get all fields of data from form
			$code = $_POST["codeInput"];

			if (strlen($code) != 0) {
				if (strlen($code) <= 15) {
					try {
						$updateCode = $db->prepare("UPDATE module SET module_code = :newcode WHERE module_id = :id");
						$updateCode->bindParam(":newcode", $code);
						$updateCode->bindParam(":id", $moduleId);
						$updateCode->execute();

						echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Module Code has been updated!</div>";
					} catch (Exception $error) {
						echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Module Code.</div>";
					}
				} else {
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to update Module Code.</div>";
				}
			}

			echo "<button type='button' class='btn btn-outline-info btn-lg'><a class='text-dark' href=''>Refresh to see any changes</a></button>";
			echo "<script>$('.updateCodeForm')[0].reset();</script>"; // reset form
		}
	?>
</div>

<div class="container">
    <div class="card-deck mb-3 text-left">
        <div class="card mb-4 box-shadow">
            <div class="card-header">
                <h4 class="my-0 font-weight-normal">Module details</h4>
            </div>
            <div class="card-body">
				<?php
					if ($error == true) {
						echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> Module doesn't exist.</div>";
					}
				?>
				<table class="table striped">
					<?php
						if ($error == false) {
							$moduleNames = [];

							echo "<tr><td>";
							echo "Module Name";
							echo "</td><td>";
							echo $moduleRow["module_name"];
							echo "</td><td>";
							echo "<button id='updateName' type='button' class='btn btn-info btn-sm' data-toggle='modal' data-target='#updateNameModal'>Update Record</button>";
							echo "</td></tr>";

							echo "<tr><td>";
							echo "Module Code";
							echo "</td><td>";
							echo $moduleRow["module_code"];
							echo "</td><td>";
							echo "<button id='updateCode' type='button' class='btn btn-info btn-sm' data-toggle='modal' data-target='#updateCodeModal'>Update Record</button>";
							echo "</td></tr>";
						}
					?>
				</table>
                <button type="button" class="btn btn-lg btn-block btn-outline-primary">
					<a href="modules.php?page=1">See all modules</a>
				</button>
            </div>
        </div>
    </div> <!-- end card-deck -->

	<!-- Modal: Update Name -->
	<div class="modal" id="updateNameModal" tabindex="-1" role="dialog" aria-labelledby="updateNameModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="updateNameLabel">Update Module Name</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="updateNameForm" class="updateNameForm" action="?id=<?php echo $moduleId ?>" method="post">
						<div class="form-group">
							<label for="nameInput">Name</label>
							<input type="text" class="form-control" name="nameInput" minlength="1" maxlength="75">
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

	<!-- Modal: Update Code -->
	<div class="modal" id="updateCodeModal" tabindex="-1" role="dialog" aria-labelledby="updateCodeModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="updateCodeLabel">Update Module Code</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="updateCodeForm" class="updateCodeForm" action="?id=<?php echo $moduleId ?>" method="post">
						<div class="form-group">
							<label for="codeInput">Code</label>
							<input type="text" class="form-control" name="codeInput" minlength="1" maxlength="15">
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" name="codeForm" form="updateCodeForm" class="btn btn-primary" onclick="return confirm('Are you sure?')" value="Save changes">
				</div>
			</div>
		</div>
	</div>

<?php 
	require_once("footer-inc.php"); 
?>