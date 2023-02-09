<?php 
	// include database connection script
	require_once("db-connect-inc.php");
	// include the header file
	require_once("header-inc.php");
	// Update title
	$pagetitle = "View Tutors";

	$currentPage = isset($_GET["page"]) ? $_GET["page"] : 1; // if page in url then use that as page, if not then set to 1 
	$perPage = 20;
	$count = $db->query("SELECT count(*) FROM user WHERE role = 2");
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
    <h1 class="display-4">View Tutors</h1>
    <p class="lead">This system allows management of courses, modules, students etc.</p>
	<?php
		if(isset($_POST["deleteTutor"])) {
			$deleteId = trim($_POST["deleteTutor"], "Delete Tutor ");

			$addressQuery = $db->prepare("SELECT address_id FROM user WHERE user_id = :id");
			$addressQuery->bindParam(":id", $deleteId);
			$addressQuery->execute();
			$addressData = $addressQuery->fetch(PDO::FETCH_ASSOC);
			$addressId = $addressData["address_id"];

			$userSQL = "DELETE FROM user WHERE user_id = :id";
			$tutorSQL = "DELETE FROM tutor_department WHERE user_id = :id";
			$addressSQL = "DELETE FROM address WHERE address_id = :addressid";

			$userDelete = $db->prepare($userSQL);
			$userDelete->bindParam(":id", $deleteId);

			$db->query("SET foreign_key_checks = 0");
			$db->beginTransaction();

			try {
				$userDeleteSuccess = $userDelete->execute();
			} catch (Exception $error) {
				echo $error;
			}

			if (!$userDeleteSuccess) {
				$db->rollBack();
				echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to delete Tutor.</div>";
			} else {
				try {
					$tutorDelete = $db->prepare($tutorSQL);
					$tutorDelete->bindParam(":id", $deleteId);
					$tutorDeleteSuccess = $tutorDelete->execute();
				} catch (Exception $error) {
					echo $error;
				}

				if (!$tutorDeleteSuccess) {
					$db->rollBack();
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to delete Tutor.</div>";
				} else {
					try {
						$addressDelete = $db->prepare($addressSQL);
						$addressDelete->bindParam(":addressid", $addressId);
						$addressDeleteSuccess = $addressDelete->execute();
					} catch (Exception $error) {
						echo $error;
					}

					if (!$addressDeleteSuccess) {
						$db->rollBack();
						echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to delete Tutor.</div>";
					} else {
						$db->commit();
						echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Tutor has been deleted!</div>";
					}
				}
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
					<a href="tutor_create.php" class="text-white">Create Tutor</a>
				</button>
                <h4 class="my-0 font-weight-normal">Tutors</h4>
                <b>Order by:
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $currentPage ?>&order=<?php echo orderType(); ?>&by=first_name">
						<?php
							if ($_GET["by"] == "first_name") {
								if ($_GET["order"] == "ASC") echo "🠕";
								else echo "🠗";
							}
						?>	
						Name
					</a>
					 :: 
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $currentPage ?>&order=<?php echo orderType(); ?>&by=user_id">
						<?php
							if ($_GET["by"] == "user_id") {
								if ($_GET["order"] == "ASC") echo "🠕";
								else echo "🠗";
							}
						?>	
						User Id
					</a>
                </b>
            </div>

			<div class="card-body">
				<?php
					if (isset($_GET["by"])) $by = $_GET["by"];
					else $by = "first_name";
					$order = $_GET["order"];

					$starting = ($currentPage - 1) * $perPage;

					try {
						$tutorRole = 2;
						$tutors = $db->prepare("SELECT user_id, first_name, last_name FROM user WHERE role = :role ORDER BY " . $by . " " . $order . " LIMIT " . $starting . "," . $perPage);
						$tutors->bindParam(":role", $tutorRole);
						$tutors->execute();
					} catch (Exception $error) {
						echo $error->getMessage();
					}
				?>
                <table class="table striped">
					<?php
						while ($tutor = $tutors->fetch(PDO::FETCH_ASSOC)) {
							echo "<tr><td>";
							echo "<a href='tutor.php?id=" . $tutor["user_id"] . "' class='text-dark'>";
							echo $tutor["first_name"] . " " . $tutor["last_name"];
							echo "</a>";
							echo "</td><td>";
							echo $tutor["user_id"];
							echo "</td><td>";
							echo "<form method='post' onSubmit='return confirm(\"Are you sure you wish to delete?\");'><input type='submit' name='deleteTutor' value='Delete Tutor " . $tutor["user_id"] . "' class='btn btn-outline-danger btn-sm float-right'></form>";
							echo "</td></tr>";
						}
					?>
            	</table>
				<nav aria-label="Page navigation">
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