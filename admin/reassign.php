<?php
require '../includes/auth.php';
require '../db.php';

if(($_SESSION['role'] ?? '') !== 'super_admin'){
    die('Access Denied');
}

$reports = $db->reports->find([], ['sort'=>['created_at'=>-1]]);
?>

<!DOCTYPE html>
<html>
<head>
<title>Case Reassignment</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{margin:0;font-family:'Segoe UI';background:#f4f6f5;}
.main{padding:25px;}
.card{background:white;padding:20px;border-radius:16px;margin-bottom:15px;box-shadow:0 5px 15px rgba(0,0,0,.05);}
select,button{padding:10px;border-radius:8px;}
button{background:#2d6a4f;color:white;border:none;}
</style>
</head>

<body>

<div class="main">
<h1>Case Reassignment</h1>

<?php foreach($reports as $r): ?>
<div class="card">
<h3><?= $r['animal'] ?></h3>
<p><?= $r['description'] ?></p>

<form method="POST">
<select name="category">
<option value="domestic">Domestic</option>
<option value="wildlife">Wildlife</option>
</select>
<button>Reassign</button>
</form>
</div>
<?php endforeach; ?>

</div>

</body>
</html>
