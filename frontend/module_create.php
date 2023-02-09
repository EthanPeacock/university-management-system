<?php 
	// include database connection script
	require_once("db-connect-inc.php");
	// include the header file
	require_once("header-inc.php");
	// Update title
	$pagetitle = "Create Module";
?>

<!-- main heading -->
<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
    <h1 class="display-4">Create a Module</h1>
    <p class="lead">This system allows management of courses, modules, students etc.</p>
	<?php
		// form submit handling
		if(isset($_POST["formSubmit"])){ // has the form been submitted
			// get all fields of data from form
			$moduleName = $_POST["nameInput"];
			$moduleCode = $_POST["codeInput"];

			// validation
			if (strlen($moduleName) <= 75 ||strlen($moduleCode) <= 15) {
				try {
					$moduleCreate = $db->prepare("INSERT INTO module (`module_name`, `module_code`) VALUES (:name, :code)");
					$moduleCreate->bindParam(":name", $moduleName);
					$moduleCreate->bindParam(":code", $moduleCode);
					$moduleCreate->execute();

					echo "<script>$('.moduleForm')[0].reset();</script>";
					echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Module has been created!</div>";
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
                <h4 class="my-0 font-weight-normal">Create Module</h4>
            </div>

			<div class="card-body">
				<form class="moduleForm" action="" method="post">
					<div class="form-group">
						<label for="nameInput">Module Name</label>
						<input type="text" class="form-control" name="nameInput" placeholder="Database Systems" required minlength="1" maxlength="75">
					</div>
					<div class="form-group">
						<label for="codeInput">Module Code</label>
						<input type="text" class="form-control" name="codeInput" placeholder="COMS404" required minlength="1" maxlength="15">
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