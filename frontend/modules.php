<?php 
	// include database connection script
	require_once("db-connect-inc.php");
	// include the header file
	require_once("header-inc.php");
	// Update title
	$pagetitle = "View Modules";

	$currentPage = isset($_GET["page"]) ? $_GET["page"] : 1; // if page in url then use that as page, if not then set to 1 
	$perPage = 20;
	$count = $db->query("SELECT count(*) FROM module");
	$total = $count->fetchColumn();
	$pages = ceil($total / $perPage);
?>

<?php
	function orderType() {
		if ($_GET["order"] == "ASC") return "DESC";
		else return "ASC";
	}
?>

<!-- main heading -->
<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
    <h1 class="display-4">View Modules</h1>
    <p class="lead">This system allows management of courses, modules, students etc.</p>
	<?php
		if(isset($_POST["deleteModule"])) {
			$failed = false;
			$deleteId = trim($_POST["deleteModule"], "Delete Module ");

			$courseModuleSQL = "DELETE FROM course_module WHERE module_id = :id";
			$particpantSQL = "DELETE FROM module_particpant WHERE module_id = :id";
			$moduleSQL = "DELETE FROM module WHERE module_id = :id";

			try {
				$courseModuleDelete = $db->prepare($courseModuleSQL);
				$courseModuleDelete->bindParam(":id", $deleteId);
	
				$db->query("SET foreign_key_checks = 0");
				$db->beginTransaction();
	
				$courseModuleDeleteSuccess = $courseModuleDelete->execute();
			} catch (Exception $error) {
				$failed = true;
			}

			if (!$failed) {
				if (!$courseModuleDeleteSuccess) {
					$failed = true;
				} else {
					try {
						$moduleDelete = $db->prepare($moduleSQL);
						$moduleDelete->bindParam(":id", $deleteId);
						$moduleDeleteSuccess = $moduleDelete->execute();
					} catch (Exception $error) {
						$failed = true;
					}

					if (!$moduleDeleteSuccess) {
						$failed = true;
					}
				}
			}

			if (!$failed) {
				try {
					$particpantDelete = $db->prepare($particpantSQL);
					$particpantDelete->bindParam(":id", $deleteId);
					$particpantDeleteSuccess = $particpantDelete->execute();
				} catch (Exception $error) {
					$failed = true;
				}

				if (!$particpantDeleteSuccess) {
					$failed = true;
				}
			}

			if ($failed) {
				$db->rollBack();
				echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to delete Module.</div>";
			} else {
				$db->commit();
				echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Module has been deleted!</div>";
			}

			$db->query("SET foreign_key_checks = 1");
		}
	?>
</div>

<div class="container">
    <div class="card-deck mb-3 text-left">
        <!-- card one -->
        <div class="card mb-4 box-shadow">
            <div class="card-header">
				<button class="btn btn-lg btn-primary float-right">
					<a href="module_create.php" class="text-white">Create Module</a>
				</button>
                <h4 class="my-0 font-weight-normal">Modules</h4>
                <b>Order by:
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $currentPage ?>&order=<?php echo orderType(); ?>&by=module_name">
						<?php
							if ($_GET["by"] == "module_name") {
								if ($_GET["order"] == "ASC") echo "🠕";
								else echo "🠗";
							}
						?>	
						Name
					</a>
					 :: 
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $currentPage ?>&order=<?php echo orderType(); ?>&by=module_id">
						<?php
							if ($_GET["by"] == "module_id") {
								if ($_GET["order"] == "ASC") echo "🠕";
								else echo "🠗";
							}
						?>	
						Module Id
					</a>
                </b>
            </div>

			<div class="card-body">
				<?php
					if (isset($_GET["by"])) $by = $_GET["by"];
					else $by = "module_name";
					$order = $_GET["order"];

					$starting = ($currentPage - 1) * $perPage;

					try {
						$modules = $db->prepare("SELECT module_id, module_name FROM module ORDER BY " . $by . " " . $order . " LIMIT " . $starting . "," . $perPage);
						$modules->execute();
					} catch (Exception $error) {
						echo $error->getMessage();
					}
				?>
                <table class="table striped">
					<?php
						while ($module = $modules->fetch(PDO::FETCH_ASSOC)) {
							echo "<tr><td>";
							echo "<a href='module.php?id=" . $module["module_id"] . "' class='text-dark'>";
							echo $module["module_name"];
							echo "</a>";
							echo "</td><td>";
							echo $module["module_id"];
							echo "</td><td>";
							echo "<form method='post' onSubmit='return confirm(\"Are you sure you wish to delete?\");'><input type='submit' name='deleteModule' value='Delete Module " . $module["module_id"] . "' class='btn btn-outline-danger btn-sm float-right'></form>";
							echo "</td></tr>";
						}
					?>
            	</table>
				<nav aria-label="Page navigation example">
					<ul class="pagination">
						<?php
							for ($p = 1; $p <= $pages ; $p++) {
								if ($_GET["order"] == "" || $_GET["by"] == "") {
									$href = "?page=" . $p;
								} else {
									$href = "?page=" . $p . "&order=" . $_GET["order"] . "&by=" . $_GET["by"];
								}
								echo "<li class='page-item'>";
								echo "<a class='page-link' href='" . $href ."'>" . $p . "</a>";
								echo "</li>";
							}
						?>
					</ul>
				</nav>
            </div>

        </div>
    </div> <!-- end card-deck -->

<?php 
    // include the footer file
    require_once("footer-inc.php"); 
?>