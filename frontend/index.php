<?php 
	// include database connection script
	require_once("db-connect-inc.php");
	// include the header file
	require_once("header-inc.php"); 
?>

<div class="pricing-header px-3 py-3 mx-auto text-center">
	<h1 class="display-4">University database</h1>
	<p class="lead">This system allows management of courses, modules, students etc.</p>
</div>

<div class="container">
    <div class="card-deck mb-3 text-center">

		<!-- Users card -->
        <div class="card mb-4 box-shadow">
            <div class="card-header">
                <h4 class="my-0 font-weight-normal">Users</h4>
            </div>
            <div class="card-body">
				<?php
					$users = $db->query("SELECT * FROM user");
					$userCount = $users->rowCount();
					echo "<h1 class='card-title uni-card-title'>Total:<br>" . $userCount . "</h1>";
				?>
                <button type="button" class="btn btn-lg btn-block btn-primary">
					<a class="text-white" href="users.php?page=1">All Users</a>
				</button>
            </div>
        </div>

		<!-- Students card -->
        <div class="card mb-4 box-shadow">
            <div class="card-header">
                <h4 class="my-0 font-weight-normal">Students</h4>
            </div>
            <div class="card-body">
				<?php
					$studentRoleId = 1;
					$userTypeCount = $db->prepare("SELECT * FROM user WHERE role = :role");
					$userTypeCount->bindParam(":role", $studentRoleId);
					$userTypeCount->execute();
					$studentCount = $userTypeCount->rowCount();
					echo "<h1 class='card-title uni-card-title'>Total:<br>" . $studentCount . "</h1>";
				?>
                <button type="button" class="btn btn-lg btn-block btn-primary">
					<a class="text-white" href="students.php?page=1">All Students</a>
				</button>
            </div>
        </div>

		<!-- Tutors card -->
        <div class="card mb-4 box-shadow">
            <div class="card-header">
                <h4 class="my-0 font-weight-normal">Tutors</h4>
            </div>
            <div class="card-body">
				<?php
					$tutorRoleId = 2;
					$userTypeCount->bindParam(":role", $tutorRoleId);
					$userTypeCount->execute();
					$tutorCount = $userTypeCount->rowCount();
					echo "<h1 class='card-title uni-card-title'>Total:<br>" . $tutorCount . "</h1>";
				?>
                <button type="button" class="btn btn-lg btn-block btn-primary">
					<a class="text-white" href="tutors.php?page=1">All Tutors</a>
				</button>
            </div>
        </div>

		<!-- Department card -->
        <div class="card mb-4 box-shadow">
            <div class="card-header">
                <h4 class="my-0 font-weight-normal">Departments</h4>
            </div>
            <div class="card-body">
                <?php
					$departments = $db->query("SELECT * FROM department");
					$departmentCount = $departments->rowCount();
					echo "<h1 class='card-title uni-card-title'>Total:<br>" . $departmentCount . "</h1>";
				?>
                <button type="button" class="btn btn-lg btn-block btn-primary">
					<a class="text-white" href="departments.php?page=1">All Departments</a>
				</button>
            </div>
        </div>

		<!-- Courses card -->
        <div class="card mb-4 box-shadow">
            <div class="card-header">
                <h4 class="my-0 font-weight-normal">Courses</h4>
            </div>
            <div class="card-body">
                <?php
					$courses = $db->query("SELECT * FROM course");
					$courseCount = $courses->rowCount();
					echo "<h1 class='card-title uni-card-title'>Total:<br>" . $courseCount . "</h1>";
				?>
                <button type="button" class="btn btn-lg btn-block btn-primary">
					<a class="text-white" href="courses.php?page=1">All Courses</a>
				</button>
            </div>
        </div>

		<!-- Module card -->
        <div class="card mb-4 box-shadow">
            <div class="card-header">
                <h4 class="my-0 font-weight-normal">Modules</h4>
            </div>
            <div class="card-body">
                <?php
					$modules = $db->query("SELECT * FROM module");
					$moduleCount = $modules->rowCount();
					echo "<h1 class='card-title uni-card-title'>Total:<br>" . $moduleCount . "</h1>";
				?>
                <button type="button" class="btn btn-lg btn-block btn-primary">
					<a class="text-white" href="modules.php?page=1">All Modules</a>
				</button>
            </div>
        </div>

    </div>
<!-- end of this div is in footer -->

<?php 
	// footer
	require_once('footer-inc.php');
?>