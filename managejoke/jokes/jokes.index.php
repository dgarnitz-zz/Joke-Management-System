<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/specialchars.inc.php';

//When the user clicks "add new joke", load the form with the correct data ready
if (isset($_GET['add']))
{
	$pageTitle = 'New Joke';
	$action = 'addform';
	$text = '';
	$authorid = '';
	$id = '';
	$button = 'Add Joke';	
	
	include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';

	//build list of authors
	try
	{
		$result = $pdo->query('SELECT id, name FROM author');
	}
	catch (PDOException $e)
	{
		$error = 'Error fetching authors from the database!';
		include 'error.php';
		exit();
	}

	foreach ($result as $row)
	{
		$authors[] = array('id' => $row['id'], 'name' => $row['name']);
	}

	//build list of categories
	try
	{
		$result = $pdo->query('SELECT id, name FROM category');
	}
	catch (PDOException $e)
	{
		$error = 'Error fetching categories from the database!';
		include 'error.php';
		exit();
	}

	foreach ($result as $row)
	{
		$categories[] = array(
			'id' => $row['id'], 
			'name' => $row['name'], 
			'selected' => FALSE);
			//The default value of each category is false meaning by default, no boxes will be checked
	}
	
	include 'jokes.form.html.php';
	exit();
}

//Add new joke to database after submitting the form
if(isset($_GET['addform']))
{
	include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';
	
	if($_POST['author'] == "") 
	{
		$error = 'You must chose an author for this joke';
		include 'error.php';
		exit();
	}
	
	try
	{
		$sql = 'INSERT INTO joke SET 
			joketext = :joketext,
			jokedate = CURDATE(),
			authorid = :authorid';
		$s = $pdo->prepare($sql);
		$s->bindvalue(':joketext', $_POST['text']);
		$s->bindvalue(':authorid', $_POST['author']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$error = 'Error adding submitted joke';
		include 'error.php';
		exit();
	}
	
	$jokeid = $pdo->lastInsertId();
	
	if (isset($_POST['categories']))
	{
		try
		{
			$sql = 'INSERT INTO jokecategory SET
				jokeid = :jokeid,
				categoryid = :categoryid';
			$s = $pdo->prepare($sql);
			
			//why not use fetchAll, the way the other loop did? (look it up, see why)
			//why is not use the foreach ($variable as $row) format? (look up documentation from PHP)
			foreach ($_POST['categories'] as $categoryid)
			{
				$s->bindValue(':jokeid', $jokeid);
				$s->bindValue(':categoryid', $categoryid);
				$s->execute();
			}
		}
		catch (PDOException $e)
		{
			$error = 'Error inserting joke into selected categories';
			include 'error.php';
			exit();
		}
	}		
				
	header('Location: . ');
	exit();
}

//Edit a joke when a user submits the edit form 
if (isset($_GET['editform']))
{
	include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';
	
	if($_POST['author'] == "")
	{
		$error = 'You must choose an author for this joke.';
		include 'error.php';
		exit();
	}
	
	try
	{
		$sql = 'UPDATE joke SET 
		joketext = :joketext,
			authorid = :authorid
			WHERE id = :id';
		$s = $pdo->prepare($sql);
		$s->bindvalue(':id', $_POST['id']);
		$s->bindvalue(':joketext', $_POST['text']);
		$s->bindValue(':authorid', $_POST['author']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$error = 'Error updating submitted joke';
		include 'error.php';
		exit();
	}
	
	try
	{
		$sql = 'DELETE FROM jokecategory WHERE jokeid = :id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id', $_POST['id']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$error = 'Error removing obsolete joke category entries.';
		include 'error.php';
		exit();
	}
	
	if (isset($_POST['categories']))
	{
		try
		{
			$sql = 'INSERT INTO jokecategory SET
				jokeid = :jokeid,
				categoryid = :categoryid';
			$s = $pdo->prepare($sql);
			
			//$categories is an array, so how does $_POST['categories'], which is itself an array 
			//store the values within the array?
			//Is it because $_POST['categories'] maps to the $categories array, not to it's individual values?
			foreach ($_POST['categories'] as $categoryid) 
			{
				$s->bindValue(':jokeid', $_POST['id']);
				$s->bindValue(':categoryid', $categoryid);
				$s->execute();
			}
		}
		catch (PDOException $e)
		{
			$error = 'Error insert joke into selected categories';
			include 'error.php';
			exit();
		}
	}
	
	header('Location: . ');
	exit();
}

//Load the form that allows you to edit an existing joke
if(isset($_POST['action']) && $_POST['action'] == 'Edit')
{
	include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';
	
	try
	{
		$sql = 'SELECT id, joketext, authorid FROM joke WHERE id = :id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id', $_POST['id']);
		$s->execute();
	}
	catch (PDOException $e) 
	{
		$error = 'Error fetching joke details.';
		include 'error.php';
		exit();
	}
	//this creates the row array which is then loaded into the variables listed below 
	$row = $s->fetch();
	
	$pageTitle = 'Edit Joke';
	$action = 'editform';
	$text = $row['joketext'];
	$authorid = $row['authorid'];
	$id = $row['id'];
	$button = 'Update Joke';
	
	//build list of authors
	try
	{
		$result = $pdo->query('SELECT id, name FROM author');
	}
	catch (PDOException $e)
	{
		$error = 'Error fetching authors from the database!';
		include 'error.php';
		exit();
	}

	foreach ($result as $row)
	{
		$authors[] = array('id' => $row['id'], 'name' => $row['name']);
	}
	
	//Get list of categories containing this joke
	try
	{
		$sql = 'SELECT categoryid FROM jokecategory WHERE jokeid =:id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id', $id);
		//because you are edit on particular joke with a unique id, you can store that id' value in a variable instead of an array
		$s->execute();
	}
	catch (PDOException $e)
	{
		$error = 'Error fetching list of selected categories';
		include 'error.php';
		exit();
	}
	
	foreach ($s as $row) 
	{
		$selectedCategories[]=$row['categoryid'];
	}
	
	//build list of categories
	try
	{
		$result = $pdo->query('SELECT id, name FROM category');
	}
	catch (PDOException $e)
	{
		$error = 'Error fetching categories from the database!';
		include 'error.php';
		exit();
	}

	foreach ($result as $row)
	{
		$categories[] = array(
			'id' => $row['id'], 
			'name' => $row['name'], 
			'selected' => in_array($row['id'], $selectedCategories));
	}
	
	include 'jokes.form.html.php';
	exit();
}

//Delete a joke from the database
if(isset($_POST['action']) && $_POST['action'] == 'Delete')
{
	include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';
	
	//Delete joke associations with this category
	try
	{
		$sql = 'DELETE FROM jokecategory WHERE jokeid = :id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id', $_POST['id']);
		$s->execute();
	}
	catch (PDOException $e) 
	{
		$error = "Error removing joke from categories";
		include 'error.php';
		exit();
	}
	
	//Delete the joke
	try
	{
		$sql = 'DELETE FROM joke WHERE id = :id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id', $_POST['id']);
		$s->execute();
	}
	catch (PDOException $e) 
	{
		$error = "Error deleting joke";
		include 'error.php';
		exit();
	}
	
	header('Location: . ');
	exit();
}
	
//Search Form & Query
if(isset($_GET['action']) and $_GET['action']=='search')
{
	include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';
	
	//The basic select statement
	//To start, define a few strings that create the query when strung together
	//This is what will be searched if no search criteria is selected in the form
	$select = 'SELECT id, joketext';
	$from = ' FROM joke';
	$where = ' WHERE TRUE';
	
	$placeholders = array();
	
	if ($_GET['author'] != '') //If the value is not blank, an author is selected 
	{
		$where .= " AND authorid = :authorid";
		$placeholders[':authorid'] = $_GET['author'];
	}
	
	if ($_GET['category'] != '') //A category is selected
	{
		//add the category information on to the other information from the joke table using JOIN where joke.id = jokeid
		$from .= ' INNER JOIN jokecategory ON id = jokeid';
		//filter for the category selected by the user
		$where .= ' AND categoryid = :categoryid';
		$placeholders[':categoryid'] = $_GET['category'];
	}
	
	if ($_GET['text'] != '') //Some search text was specified
	{
		$where .= " AND joketext LIKE :joketext";
		$placeholders[':joketext'] = '%' . $_GET['text'] . '%';
	}
	
	try
	{
		$sql = $select . $from . $where;
		$s = $pdo->prepare($sql);
		$s->execute($placeholders);
	}
	catch (PDOException $e)
	{
		$error = 'Error fetching jokes';
		include 'error.php';
		exit();
	}
	
	foreach($s as $row)
	{
		$jokes[] = array('id' => $row['id'], 'text' => $row['joketext']);
	}
	
	include 'jokes.html.php';
	exit();
}

include $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/db.inc.php';

//build list of authors
try
{
	$result = $pdo->query('SELECT id, name FROM author');
}
catch (PDOException $e)
{
	$error = 'Error fetching authors from the database!';
	include 'error.php';
	exit();
}

foreach ($result as $row)
{
	$authors[] = array('id' => $row['id'], 'name' => $row['name']);
}

//build list of categories
try
{
	$result = $pdo->query('SELECT id, name FROM category');
}
catch (PDOException $e)
{
	$error = 'Error fetching categories from the database!';
	include 'error.php';
	exit();
}

foreach ($result as $row)
{
	$categories[] = array('id' => $row['id'], 'name' => $row['name']);
}

include 'searchform.html.php';
	
?>