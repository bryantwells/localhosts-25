
<?php
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		
		$renamed_items = json_decode($_POST['renamed_items']);
		$new_paths = [];

		foreach ($renamed_items as $old_path_string => $new_path_string) {

			$id = bin2hex(random_bytes(3));

			$dir = './../../content/';

			$old_path = $dir . $old_path_string;
			$new_path = $dir . $new_path_string . $id;

			$old_path_parts = pathinfo($old_path);
			$new_path_parts = pathinfo($new_path);

			$old_path_yml = $old_path_parts['dirname'] . '/' . $old_path_parts['filename'] . '.yml';
			$new_path_yml = $new_path_parts['dirname'] . '/' . $new_path_parts['filename'] . '.yml' . $id;

			rename($old_path, $new_path);
			$new_paths[] = $new_path;

			if (file_exists($old_path_yml)) {
				rename($old_path_yml, $new_path_yml);
				$new_paths[] = $new_path_yml;
			}
		}

		foreach($new_paths as $new_path) {
			rename($new_path, substr($new_path, 0, -6));
		}

		echo 'OK';
	}
?>