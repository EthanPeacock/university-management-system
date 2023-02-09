<?php
if (!isset($pagetitle)) $pagetitle = "COMS404 Assignment";
?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title><?php if (isset($pagetitle)) echo $pagetitle;?> </title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<link href="custom.css" rel="stylesheet">
	</head>

	<body>

	<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom box-shadow">
		<h5 class="my-0 mr-md-auto font-weight-normal">COMS404 Database Systems</h5>
		<nav class="my-2 my-md-0 mr-md-3">
			<a class="p-2 text-dark" href="students.php">Students</a>
			<a class="p-2 text-dark" href="tutors.php">Tutors</a>
			<a class="p-2 text-dark" href="departments.php">Departments</a>
			<a class="p-2 text-dark" href="courses.php">Courses</a>
			<a class="p-2 text-dark" href="modules.php">Modules</a>
		</nav>
		<a class="btn btn-outline-primary" href="index.php">Home</a>
	</div>
