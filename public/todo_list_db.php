<?php

$dbc = new PDO('mysql:host=127.0.0.1;dbname=todo_list', 'lori', 'password');

$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$to_do_items = [];

require_once('classes/filestore.php');

if (!empty($_POST)) {
	if (isset($_POST['TODO_item'])) {
		$stmt = $dbc->prepare("INSERT INTO todo (todo)
					           VALUES (:todo)");
		$stmt->bindValue(':todo', $_POST['TODO_item'], PDO::PARAM_STR);

		$stmt->execute();
	}

	if (isset($_POST['remove'])) {
		$stmt = $dbc->prepare("DELETE FROM todo
					          WHERE id = :id");
		$stmt->bindValue(':id', $_POST['remove'], PDO::PARAM_INT);

		$stmt->execute();
	}			
}

if (count($_FILES) > 0 && $_FILES['file1']['error'] == 0)
{
	if ($_FILES['file1']['type'] == 'text/plain') {
	    $upload_dir = '/vagrant/sites/todo.dev/public/uploads/';
	    $filename1 = basename($_FILES['file1']['name']);
	    $saved_filename = $upload_dir . $filename1;
	    move_uploaded_file($_FILES['file1']['tmp_name'], $saved_filename);
	    $add_new_file = new filestore($saved_filename);
		$newfile = $add_new_file->read();
		foreach ($newfile as $newitem) {
		$stmt = $dbc->prepare("INSERT INTO todo (todo)
					           VALUES (:todo)");
		$stmt->bindValue(':todo', $newitem, PDO::PARAM_STR);

		$stmt->execute();	
			}
	} else {
		echo "This is not a text file, please try again!";
	}
}

if (!empty($_GET)) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
$pageNext = $page + 1;
$pagePrev = $page - 1;

$limit = 10;
$offset = (($limit * $page) - $limit); 

$stmt = $dbc->prepare("SELECT * FROM todo LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
	<head>
		<title>TODO List</title>
	</head>
	<body>
		<h2>TODO List</h2>
		<?if (isset($msg)) {
			echo $msg;
		}?>
 	<ul>
 	
		<?	foreach($todos as $item): ?>
				 <li><?= htmlspecialchars(strip_tags($item['todo']));?>
				 	<button class="btn-remove" data-todo="<?= $item['id']; ?>">Remove</button></li> 	
		<?endforeach;?>
	</ul>

<!-- Table to remove item -->
		<form id="removeForm" action="todo_list_db.php" method="post">
    		<input id="removeId" type="hidden" name="remove" value="">	
		</form>	

		<p>Please enter any additional TODO items on the form below. Please click submit when you have completed entering your items.</p>
		<form method="POST" action="todo_list_db.php">
		    <p>
		    	<label for="TODO_Item">TODO Item:</label>
		    	<input type="text" id="TODO_item" name="TODO_item" placeholder="TODO item">
		    </p>
		    <p>
			    <button type="submit">Submit</button>
			</p>
		</form>
		<h1>Upload File</h1>

	<form method="POST" enctype="multipart/form-data">
	    <p>
	        <label for="file1">File to upload: </label>
	        <input type="file" id="file1" name="file1">
	    </p>
	    <p>
	        <input type="submit" value="Upload">
	    </p>
	</form>	
	<? if ($pagePrev > 0) : ?> 
		<?= "<a href='?page=$pagePrev'>Previous</a>";?>
		<? endif ?>
		<? if ($pagePrev < 2) : ?>
		<?= "<a href='?page=$pageNext'>Next</a>";?>
		<? endif ?>
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script>

	$('.btn-remove').click(function () {
	    var todoId = $(this).data('todo');
	    
	    if (confirm('Are you sure you want to remove item ' + todoId + '?')) {
	        $('#removeId').val(todoId);
	        $('#removeForm').submit();
	    }
	});
	</script>
	</body>
</html>