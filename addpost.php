<?php 
session_start();
include('db.php');
if (!empty($_SESSION['user'])) 
{
	include('apps/header.php');
	$title = '';
	$description = '';
	if (count($_POST) > 0) 
	{
		function clean($value = "") 
		{
		    $value = trim($value);
		    $value = stripslashes($value);
		    $value = strip_tags($value);
		    $value = htmlspecialchars($value);
		    
		    return $value;
		}
			$title = clean($_POST['title']);
			$description = clean($_POST['description']);
		if (!empty($_POST['description']) && !empty($_FILES)) 
		{
			if (empty($title)) 
			{
				$temp = substr($description, 0, 30);
				$title = substr($temp, 0, strrpos($temp, ' '));
			}
			if (strlen($title) > 100 || strlen($title) < 5)
			{
				echo 'Заголовок должен варироватся от 10 до 100 символов';
			}
			if (strlen($description) > 6000 || strlen($description) < 250)
			{
				echo 'Размер стати должен быть от 500 до 6000 символов';
			}
			$category = $_POST['category'];
			$autor = $_SESSION['user']['name'];
			$autorid = $_SESSION['user']['id'];
			if($_FILES['image']['type'] == "image/jpg") 
			{
				$ending = '.jpg';
			}
			elseif ($_FILES['image']['type'] == "image/png") 
			{
				$ending = '.png';
			}
			elseif ($_FILES['image']['type'] == "image/jpeg") 
			{
				$ending = '.jpeg';
			}
			else
			{
				$ending = NULL;
			}
			if ($ending == NULL) 
			{
				echo "Фото не найдено или имеет не коректное расширение. Изображение может иметь только расширение .jpg, .jpeg, .png";
			}
			else
			{
				mysqli_query($connection, "INSERT INTO `post` (`title`, `text`, `category`, `autor`, `autorid`) VALUES ('$title', '$description', '$category', '$autor', '$autorid');");
				$lastid = mysqli_insert_id($connection);
				$uploaddir = 'images/';
				$uploadfile = $uploaddir . "postid-$lastid$ending";
				$tmp_name = $_FILES['image']['tmp_name'];
				move_uploaded_file($tmp_name, $uploadfile);
				mysqli_query($connection, "UPDATE `post` SET `image` = '$uploadfile' WHERE `post`.`id` = '$lastid';");
				echo
				"<script type='text/javascript'>
		        window.location = 'post.php?id=$lastid'
		        </script>";
				unset($_POST);
			}
		}
		else
		{
			echo 'Заполните все поля!';
		}		
	}
?>

	<div class="center">
		<form class="addpost" method="POST" enctype="multipart/form-data">
			<label for="name">Заголовок:</label>
			<input name="title" type="text" value="<?php echo $title; ?>">
			<label for="description">Текст:</label>
			<textarea name="description" cols="30" rows="10" required><?php echo $description; ?></textarea>
			<label for="image">Лицевая картинка:</label>
			<input class="file" name="image" type="file" required>
			<label for="category">Категория:</label>
			<select name="category">
				<?php  
					$query = mysqli_query($connection, "SELECT `name` FROM `category` ORDER BY `category`.`name` ASC;");
					while ($categ = mysqli_fetch_assoc($query)) 
					{
						echo '<option>' . $categ['name'] . '</option>';
					}
				?>
			</select>
			<input class="submit" type="submit" value="Добавить">
		</form>
	</div>

<?php 
	include('apps/footer.php');
}
else
{
	header("Location: login.php");
}
?>
