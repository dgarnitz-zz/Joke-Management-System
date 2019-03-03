<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/deletejoke/specialchars.inc.php'; ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?php htmlout($pageTitle); ?></title>
		<style type="text/css">
		textarea {
			display: block;
			width: 100%;
		}
		</style>
	</head>
	<body>
		<h1><?php htmlout($pageTitle); ?></h1>
		<form action="?<?php htmlout($action); ?>" method="post">
			<div>
				<label for="text">Type your joke here:</label>
				<textarea id="text" name="text" rows="3" cols="40">
					<?php htmlout($text); ?>
				</textarea>
			</div>
			<div>
				<label for="author">Author:</label>
				<select name="author" id="author">
				<option value="">Select</option>
				<?php foreach ($authors as $author): ?>
				<option value="<?php htmlout($author['id']); ?>"<?php 
					if ($author['id']==$authorid) 
					{
						echo ' selected';
					}
					?>><?php htmlout($author['name']); ?></option>
				<?php endforeach; ?>
				</select>
			</div>
			<fieldset> <!--This groups a series of related elements together by putting a box around them-->
				<legend>Categories:</legend> <!--the legend tag puts a caption on a fieldset box -->
				<?php foreach ($categories as $category): ?>
				<div>
					<label for="category<?php htmlout($category['id']); ?>">
					<input type="checkbox" name="categories[]" 
					id="category<?php htmlout($category['id']); ?>" 
					value="<?php htmlout($category['id']); ?>" <?php 
					if ($category['selected']) {
						echo ' checked';
					}
					?>><?php htmlout($category['name']); ?>
					</label>
				</div>
				<?php endforeach; ?>
			</fieldset>
			<div>
				<input type="hidden" name="id" value="<?php htmlout($id); ?>">
				<input type="submit" value="<?php htmlout($button); ?>">
			</div>
		</form>
	</body>
</html>
				
				
				
				
				
				
				
				
				
				
				
				