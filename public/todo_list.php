<?php
define('FILENAME', 'data/list.txt');

$to_do_items = [];

// var_dump($_GET);


function read_file($filename) {
    $handle = fopen($filename, 'r');
    $todo_string = fread($handle, filesize($filename));
    fclose($handle); 
    return explode("\n", $todo_string);     
}

function write_file($filename, $array) {
    if (is_writable($filename)) {        
        $handle = fopen($filename, 'w');
        fwrite($handle, implode("\n", $array));
        fclose($handle);        
    }   
}

$to_do_items = read_file(FILENAME);

if (isset($_GET['id'])) {
	unset($to_do_items[$_GET['id']]);
	write_file(FILENAME, $to_do_items);
}

// var_dump($_POST['TODO_item']);
if (isset($_POST['TODO_item'])) {
	$item = trim($_POST['TODO_item']);
	array_push($to_do_items, $item);
	write_file(FILENAME, $to_do_items);
}

// var_dump($_FILES);

if (count($_FILES) > 0 && $_FILES['file1']['error'] == 0)
{
	if ($_FILES['file1']['type'] == 'text/plain') {
	    $upload_dir = '/vagrant/sites/todo.dev/public/uploads/';
	    $filename1 = basename($_FILES['file1']['name']);
	    $saved_filename = $upload_dir . $filename1;
	    move_uploaded_file($_FILES['file1']['tmp_name'], $saved_filename);
	} else {
		echo "This is not a text file, please try again!";
	}
}

// Check if we saved a file
if (isset($saved_filename)) {
    $file_todo = $saved_filename;
    $newfile = read_file($file_todo);
    // var_dump($newfile);
    $to_do_items = array_merge($to_do_items, $newfile);
    write_file(FILENAME, $to_do_items);
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>TODO List</title>
	</head>
	<body>
		<h2>TODO List</h2>
<ul>		
		<?	foreach($to_do_items as $key => $item): ?>
				 <li><?= "$item <a href = '?id=$key'>Mark Complete</a>"; ?></li> 	
		<?endforeach;?>
</ul>		
		<p>Please enter any additional TODO items on the form below. Please click submit when you have completed entering your items.</p>
		<form method="POST">
		    <p>
		    	<label for="TODO_Item">TODO Item:</label>s
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
	</body>
</html>