<?php

//Remove magic quotes
include $_SERVER['DOCUMENT_ROOT'] . '/magicquotes.inc.php';

//Detects if the "Add Category" hyperlink has been clicked, and loads the firm with these values
if (isset($_GET['add']))
{
	$pageTitle = 'New Category';
	$action = 'addform';
	$name='';
	$email='';
	$id='';
	$button='Add Category';
	
	//Try re-using the same form as for authors
	include 'categories.form.html.php';
	exit();
}

//Processing the "Add Category" form once it is submitted
//When the forms submits, a query string call 'addform' will be created, so you use $_GET to detect it 
if(isset($_GET['addform']))
{
	include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';
	
	try
	{
		$sql = 'INSERT INTO category SET name= :name';
		$s = $pdo->prepare($sql);
		$s->bindvalue(':name', $_POST['name']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$error = 'Error adding submitted category';
		include 'error.html.php';
		exit();
	}
	
	header('Location: . ');
	exit();
}

//Edit a category's information
if(isset($_POST['action']) && $_POST['action'] == 'Edit')
{
	include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';
	
	//Get information pertaining to a specific category
	try
	{
		//select the id, name of any category with the selected id  
		$sql = 'SELECT id, name FROM category WHERE id = :id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id', $_POST['id']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$error = "Error fetching category's information";
		include 'error.html.php';
		exit();
	}
	
	$row = $s->fetch();
	
	$pageTitle = 'Edit Category';
	$action = 'editform';
	$name = $row['name'];
	$id = $row['id'];
	$button='Update Category';
	
	//Try re-using the author form 
	include 'categories.form.html.php';
	exit();
}

//Processing the "Edit Category" form once submitted
if(isset($_GET['editform']))
{
	include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';
	
	try
	{
		$sql = 'UPDATE category SET name= :name WHERE id = :id';
		$s = $pdo->prepare($sql);
		$s->bindvalue(':id', $_POST['id']);
		$s->bindvalue(':name', $_POST['name']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$error = 'Error updating submitted category';
		include 'error.php';
		exit();
	}
	
	header('Location: . ');
	exit();
}

//Delete a category
if(isset($_POST['action']) && $_POST['action'] == 'Delete')
{
	include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';
	
	//Delete joke associations with this category
	try
	{
		$sql = 'DELETE FROM jokecategory WHERE categoryid = :id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id', $_POST['id']);
		$s->execute();
	}
	catch (PDOException $e) 
	{
		$error = "Error removing jokes from category";
		include 'error.php';
		exit();
	}
	
	//Delete the category
	try
	{
		$sql = 'DELETE FROM category WHERE id = :id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id', $_POST['id']);
		$s->execute();
	}
	catch (PDOException $e) 
	{
		$error = "Error deleting category";
		include 'error.php';
		exit();
	}
	
	header('Location: . ');
	exit();
}

//Display category list
include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';

try
{
	$result = $pdo->query('SELECT id, name FROM category');
}
catch (PDOException $e)
{
	$error = 'Error fetching categories from the database!';
	include 'error.html.php';
	exit();
}

foreach ($result as $row)
{
	$categories[] = array('id' => $row['id'], 'name' => $row['name']);
}

include 'categories.html.php';


?>