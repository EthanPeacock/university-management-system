<?php 
	// include database connection script
	require_once("db-connect-inc.php");
	// include the header file
	require_once("header-inc.php");
	// Update title
	$pagetitle = "View Courses";

	$currentPage = isset($_GET["page"]) ? $_GET["page"] : 1; // if page in url then use that as page, if not then set to 1 
	$perPage = 20;
	$count = $db->query("SELECT count(*) FROM course");
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
    <h1 class="display-4">View Courses</h1>
    <p class="lead">This system allows management of courses, modules, students etc.</p>
	<?php
		if(isset($_POST["deleteCourse"])) {
			$failed = false;
			$deleteId = trim($_POST["deleteCourse"], "Delete Course ");

			$studentsCourse = $db->prepare("SELECT student_number FROM student WHERE course = :course");
			$studentsCourse->bindParam(":course", $deleteId);
			$studentsCourse->execute();
			$studentsOnCourse = $studentsCourse->rowCount();

			if ($studentsOnCourse > 0) {
				echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to delete Course - Students on course.</div>";
			} else {
				$courseModuleSQL = "DELETE FROM course_module WHERE course_id = :id";
				$courseSQL = "DELETE FROM course WHERE course_id = :id";

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
							$courseDelete = $db->prepare($courseSQL);
							$courseDelete->bindParam(":id", $deleteId);
							$courseDeleteSuccess = $courseDelete->execute();
						} catch (Exception $error) {
							$failed = true;
						}

						if (!$courseDeleteSuccess) {
							$failed = true;
						}
					}
				}

				if ($failed) {
					$db->rollBack();
					echo "<div class='alert alert-danger text-center' role='alert'><strong>Error!</strong> Unable to delete Course.</div>";
				} else {
					$db->commit();
					echo "<div class='alert alert-success text-center' role='alert'><strong>Success!</strong> Course has been deleted!</div>";
				}

				$db->query("SET foreign_key_checks = 1");
			}
		}
	?>
</div>

<div class="container">
    <div class="card-deck mb-3 text-left">
        <!-- card one -->
        <div class="card mb-4 box-shadow">
            <div class="card-header">
				<button class="btn btn-lg btn-primary float-right">
					<a href="course_create.php" class="text-white">Create Course</a>
				</button>
                <h4 class="my-0 font-weight-normal">Courses</h4>
                <b>Order by:
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $currentPage ?>&order=<?php echo orderType(); ?>&by=course_name">
						<?php
							if ($_GET["by"] == "course_name") {
								if ($_GET["order"] == "ASC") echo "🠕";
								else echo "🠗";
							}
						?>	
						Name
					</a>
					 :: 
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $currentPage ?>&order=<?php echo orderType(); ?>&by=course_id">
						<?php
							if ($_GET["by"] == "course_id") {
								if ($_GET["order"] == "ASC") echo "🠕";
								else echo "🠗";
							}
						?>	
						Course Id
					</a>
                </b>
            </div>

			<div class="card-body">
				<?php
					if (isset($_GET["by"])) $by = $_GET["by"];
					else $by = "course_name";
					$order = $_GET["order"];

					$starting = ($currentPage - 1) * $perPage;

					try {
						$courses = $db->prepare("SELECT course_id, course_name FROM course ORDER BY " . $by . " " . $order . " LIMIT " . $starting . "," . $perPage);
						$courses->execute();
					} catch (Exception $error) {
						echo $error->getMessage();
					}
				?>
                <table class="table striped">
					<?php
						while ($course = $courses->fetch(PDO::FETCH_ASSOC)) {
							echo "<tr><td>";
							echo "<a href='course.php?id=" . $course["course_id"] . "' class='text-dark'>";
							echo $course["course_name"];
							echo "</a>";
							echo "</td><td>";
							echo $course["course_id"];
							echo "</td><td>";
							echo "<form method='post' onSubmit='return confirm(\"Are you sure you wish to delete?\");'><input type='submit' name='deleteCourse' value='Delete Course " . $course["course_id"] . "' class='btn btn-outline-danger btn-sm float-right'></form>";
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