<?php
define('FILENAME', 'data/list.txt');

$to_do_items = [];

var_dump($_GET);


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


?>
<!DOCTYPE html>
<html>
	<head>
		<title>TODO List</title>
	</head>
	<body>
		<h2>TODO List</h2>
<ul>		
		<?php		
			foreach($to_do_items as $key => $item) {
				echo "<li>$item<a href = '?id=$key'>Mark Complete</a></li>";
			} 	
		?>
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
	</body>
</html>