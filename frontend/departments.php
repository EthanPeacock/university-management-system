<?php 
	// include database connection script
	require_once("db-connect-inc.php");
	// include the header file
	require_once("header-inc.php");
	// Update title
	$pagetitle = "View Departments";

	$currentPage = isset($_GET["page"]) ? $_GET["page"] : 1; // if page in url then use that as page, if not then set to 1 
	$perPage = 20;
	$count = $db->query("SELECT count(*) FROM department");
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
    <h1 class="display-4">View Departments</h1>
    <p class="lead">This system allows management of courses, modules, students etc.</p>
</div>

<div class="container">
    <div class="card-deck mb-3 text-left">
        <!-- card one -->
        <div class="card mb-4 box-shadow">
            <div class="card-header">
                <h4 class="my-0 font-weight-normal">Departments</h4>
                <b>Order by:
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $currentPage ?>&order=<?php echo orderType(); ?>&by=dept_name">
						<?php
							if ($_GET["by"] == "dept_name") {
								if ($_GET["order"] == "ASC") echo "🠕";
								else echo "🠗";
							}
						?>	
						Name
					</a>
					 :: 
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $currentPage ?>&order=<?php echo orderType(); ?>&by=dept_id">
						<?php
							if ($_GET["by"] == "dept_id") {
								if ($_GET["order"] == "ASC") echo "🠕";
								else echo "🠗";
							}
						?>	
						Department Id
					</a>
                </b>
            </div>

			<div class="card-body">
				<?php
					if (isset($_GET["by"])) $by = $_GET["by"];
					else $by = "dept_name";
					$order = $_GET["order"];

					$starting = ($currentPage - 1) * $perPage;

					try {
						$departments = $db->prepare("SELECT dept_id, dept_name FROM department ORDER BY " . $by . " " . $order . " LIMIT " . $starting . "," . $perPage);
						$departments->execute();
					} catch (Exception $error) {
						echo $error->getMessage();
					}
				?>
                <table class="table striped">
					<?php
						while ($dept = $departments->fetch(PDO::FETCH_ASSOC)) {
							echo "<tr><td>";
							echo $dept["dept_name"];
							echo "</a>";
							echo "</td><td>";
							echo $dept["dept_id"];
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