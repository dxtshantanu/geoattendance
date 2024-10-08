<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Employee</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php
  include 'includes/session.php';
  include 'includes/header.php';  // Include header if needed
  include 'includes/navbar.php';  // Include navbar if needed
  include 'includes/menubar.php';  // Include menubar if needed

  // Connection to database (replace with your details)
  $conn = new mysqli("localhost", "your_username", "your_password", "your_database");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  if(isset($_POST['add'])){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $birthdate = $_POST['birthdate'];
    $contact = $_POST['contact'];
    $gender = $_POST['gender'];
    $position = $_POST['position'];
    $schedule = $_POST['schedule'];
    $filename = $_FILES['photo']['name'];
    if(!empty($filename)){
      move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);	
    }
    //creating employeeid
    $letters = '';
    $numbers = '';
    foreach (range('A', 'Z') as $char) {
      $letters .= $char;
    }
    for($i = 0; $i < 10; $i++){
      $numbers .= $i;
    }
    $employee_id = substr(str_shuffle($letters), 0, 3).substr(str_shuffle($numbers), 0, 9);
    //
    $sql = "INSERT INTO employees (employee_id, firstname, lastname, address, birthdate, contact_info, gender, position_id, schedule_id, photo, created_on) VALUES ('$employee_id', '$firstname', '<span class="math-inline">lastname', '</span>address', '$birthdate', '$contact', '$gender', '$position', '$schedule', '$filename', NOW())";
    if($conn->query($sql)){
      $_SESSION['success'] = 'Employee added successfully';
    }
    else{
      $_SESSION['error'] = $conn->error;
    }
  }
  else{
    $_SESSION['error'] = 'Fill up add form first';
  }

  $conn->close();  // Close the database connection
  header('location: employee.php');  // Redirect after processing
?>

<div class="container">
  <h2>Add Employee</h2>
  <?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <?php echo $_SESSION['error']; ?>
      <?php unset($_SESSION['error']); ?>
    </div>
  <?php endif; ?>
  <form action="" method="post" enctype="multipart/form-data">
    <div class="form-group">
      <label for="photo">Employee Photo:</label>
      <input type="file" name="photo" id="photo" accept="image/*">
    </div>
    <button type="submit" class="btn btn-primary" name="add">Add Employee</button>
  </form>
</div>

<div class="container mt-3">
  <p>To upload additional documents related to the new employee, you can save them to your Google Drive:</p>
  <ol>
    <li>Open your web browser and go to <a href="https://drive.google.com/drive/u/0/folders/1Hoiz5QQQR4zHwGjwwgf9IsS3tq5G_Pu_">https://www.google.com/drive/download/</a>.</li>
