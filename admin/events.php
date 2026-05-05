<?php
require '../includes/auth.php';
require '../db.php';

$collection = $db->events;

/* ADD EVENT */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $collection->insertOne([
        'title' => $_POST['title'],
        'location' => $_POST['location'],
        'date' => $_POST['date'],
        'description' => $_POST['description'],
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ]);
}

/* DELETE EVENT */
if (isset($_GET['delete'])) {
    $collection->deleteOne([
        '_id' => new MongoDB\BSON\ObjectId($_GET['delete'])
    ]);
}

$events = iterator_to_array($collection->find([]));

/* GROUP EVENTS BY DATE */
$calendarEvents = [];
foreach ($events as $e) {
    $calendarEvents[$e['date']][] = $e;
}

$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html>
<head>
<title>Advanced Events</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

body{
    font-family:'Segoe UI';
    margin:0;
    background:#f4f6f5;
}

.main{
    margin-left:260px;
    padding:20px;
}

/* FORM */
.form{
    background:white;
    padding:20px;
    border-radius:12px;
    margin-bottom:20px;
}

input, textarea{
    width:100%;
    padding:10px;
    margin-bottom:10px;
    border-radius:8px;
    border:1px solid #ddd;
}

button{
    background:#2d6a4f;
    color:white;
    padding:10px;
    border:none;
    border-radius:8px;
}

/* CALENDAR */
.calendar{
    display:grid;
    grid-template-columns:repeat(7,1fr);
    gap:10px;
}

.day{
    background:white;
    padding:10px;
    border-radius:10px;
    min-height:100px;
    position:relative;
}

.today{
    border:2px solid #2d6a4f;
}

/* EVENT */
.event{
    background:#2d6a4f;
    color:white;
    padding:4px;
    border-radius:6px;
    font-size:11px;
    margin-top:5px;
}

/* STATUS */
.status{
    font-size:11px;
    margin-top:5px;
}

.upcoming{color:#2d6a4f;}
.ongoing{color:orange;}
.completed{color:#999;}

</style>
</head>

<body>

<div class="main">

<h2>📅 Event Management System</h2>

<!-- FORM -->
<form method="POST" class="form">
<input type="text" name="title" placeholder="Event Title" required>
<input type="text" name="location" placeholder="Location" required>
<input type="date" name="date" required>
<textarea name="description" placeholder="Description"></textarea>
<button>Add Event</button>
</form>

<!-- CALENDAR -->
<div class="calendar">

<?php
for ($i = 1; $i <= 31; $i++):
$date = date('Y-m-') . str_pad($i,2,'0',STR_PAD_LEFT);
?>

<div class="day <?= $date == $today ? 'today' : '' ?>">

<strong><?= $i ?></strong>

<?php if(isset($calendarEvents[$date])): ?>
    <?php foreach($calendarEvents[$date] as $e): ?>

        <?php
        $status = "upcoming";
        if($date == $today) $status = "ongoing";
        if($date < $today) $status = "completed";
        ?>

        <div class="event">
            <?= $e['title'] ?>
        </div>

        <div class="status <?= $status ?>">
            <?= strtoupper($status) ?>
        </div>

        <a href="?delete=<?= $e['_id'] ?>" style="font-size:10px;color:red;">Delete</a>

    <?php endforeach; ?>
<?php endif; ?>

</div>

<?php endfor; ?>

</div>

</div>

</body>
</html>
```
