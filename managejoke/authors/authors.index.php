<?php

//Remove magic quotes
include $_SERVER['DOCUMENT_ROOT'] . '/magicquotes.inc.php';

//detect if the user has clicked the "Add Author" button
//set the variables that will display in the "Add Author" form 
if (isset($_GET['add']))
{
	$pageTitle = 'New Author';
	$action = 'addform';
	//This line above will set the form's action attribute equal to 'addform'
	//meaning that the URL's query string will be 'addform'
	//and PHP will automatically create a string variable called 'addform' and store it in the $_GET array
	$name='';
	$email='';
	$id='';
	$button='Add Author';
	
	include 'author.form.html.php';
	exit();
}

//Processing the "Add Author" form once it is submitted
//When the forms submits, a query string call 'addform' will be created, so you use $_GET to detect it 
if(isset($_GET['addform']))
{
	include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';
	
	try
	{
		$sql = 'INSERT INTO author SET name= :name, email = :email';
		$s = $pdo->prepare($sql);
		$s->bindvalue(':name', $_POST['name']);
		$s->bindvalue(':email', $_POST['email']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$error = 'Error adding submitted user';
		include 'error.html.php';
		exit();
	}
	
	header('Location: . ');
	exit();
}

//Processing the "Edit Author" form once submitted
if(isset($_GET['editform']))
{
	include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';
	
	try
	{
		$sql = 'UPDATE author SET name= :name, email = :email WHERE id = :id';
		$s = $pdo->prepare($sql);
		$s->bindvalue(':id', $_POST['id']);
		$s->bindvalue(':name', $_POST['name']);
		$s->bindvalue(':email', $_POST['email']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$error = 'Error updating submitted author';
		include 'error.html.php';
		exit();
	}
	
	header('Location: . ');
	exit();
}

//Edit an author's information
if(isset($_POST['action']) && $_POST['action'] == 'Edit')
{
	include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';
	
	//Get information pertaining to a specific author
	try
	{
		//select the id, name and email of any author with the selected id  
		$sql = 'SELECT id, name, email FROM author WHERE id = :id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id', $_POST['id']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$error = "Error fetching author's information";
		include 'error.html.php';
		exit();
	}
	
	$row = $s->fetch();
	
	$pageTitle = 'Edit Author';
	$action = 'editform';
	//This line above will set the form's action attribute equal to 'editform'
	//meaning that the URL's query string will be 'editform'
	//and PHP will automatically create a string variable called 'editform' and store it in the $_GET array
	$name = $row['name'];
	$email = $row['email'];
	$id = $row['id'];
	$button='Update Author';
	
	include 'author.form.html.php';
	exit();
}


//Delete an author
if(isset($_POST['action']) && $_POST['action'] == 'Delete')
{
	include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';
	
	//Get jokes belonging to author
	try
	{
		//select any joke's ID where the authorid in the same row equals the value in our author table's id column
		$sql = 'SELECT id FROM joke WHERE authorid = :id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id', $_POST['id']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$error = "Error getting list of jokes to delete";
		include 'error.html.php';
		exit();
	}
	
	$result = $s->fetchAll();
	
	//Delete joke category entries
	try
	{
		$sql = 'DELETE FROM jokecategory WHERE jokeid = :id';
		$s = $pdo->prepare($sql);
		
		//For each joke
		//Where $result is an array containing all the joke IDs associated with a particular author's ID
		//Because each joke has a unique ID, it is necessary to have a foreach loop that runs the query 
		//for each unique ID 
		foreach ($result as $row)
		{
			$jokeId = $row['id'];
			$s->bindValue(':id', $jokeId);
			$s->execute();
		}
	}
	catch (PDOException $e)
	{
		$error = 'Error deleting category entries for joke';
		include 'error.html.php';
		exit();
	}
	
	//Delete jokes belonging to author
	try
	{
		$sql = 'DELETE FROM joke WHERE authorid = :id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id', $_POST['id']);
		$s->execute();
	}
	catch (PDOException $e) 
	{
		$error = 'Error deleting jokes for author.';
		include 'error.html.php';
		exit();
	}
	
	//Delete the author
	try
	{
		$sql = 'DELETE FROM author WHERE id = :id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id', $_POST['id']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$error = 'Error deleting author.';
		include 'error.html.php';
		exit();
	}
	
	header('Location: . ');
	exit();
}

//Display author list
include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';

try
{
	$result = $pdo->query('SELECT id, name FROM author');
}
catch (PDOException $e)
{
	$error = 'Error fetching authors from the database!';
	include 'error.html.php';
	exit();
}

foreach ($result as $row)
{
	$authors[] = array('id' => $row['id'], 'name' => $row['name']);
}

include 'authors.html.php';


?>