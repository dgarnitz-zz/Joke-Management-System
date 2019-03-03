<?php

include $_SERVER['DOCUMENT_ROOT'] . '/magicquotes.inc.php';

if (isset($_GET['addjoke']))
{
	include 'addjoke.html.php';
	exit();
}

if (isset($_POST['joketext']))
{
	include 'db.inc.php';
	
	try
	{
		$sql = 'INSERT INTO joke SET joketext = :joketext, jokedate = CURDATE()';
		$s = $pdo->prepare($sql);
		$s->bindValue(':joketext', $_POST['joketext']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$error = 'Error adding submitted joke';
		include 'error.php';
		exit();
	}
	
	header('Location: .');
	exit();
}

if (isset($_GET['deletejoke']))
{
	include 'db.inc.php';
	
	try{
		$sql = 'DELETE FROM joke WHERE id = :id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id', $_POST['id']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$error = 'Error deleting joke.';
		include 'error.php';
		exit();
	}
	header('Location: .');
	exit();
}

include 'db.inc.php';

try
{
	$sql = 'SELECT joke.id, joketext, name, email FROM joke INNER JOIN author ON authorid = author.id';
	$result = $pdo->query($sql);
}
catch (PDOException $e)
{
	$error = 'Error fetching jokes.';
	include 'error.php';
	exit();
}
while ($row = $result->fetch())
{
	$jokes[]=array(
	'id' => $row['id'], 
	'text' => $row['joketext'],
	'name' => $row['name'],
	'email' => $row['email']
	);
}

include 'jokes.html.php';

?>
	