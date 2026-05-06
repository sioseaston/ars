<?php
.main{margin-left:260px;padding:25px;}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;}
.card{background:white;padding:20px;border-radius:16px;box-shadow:0 5px 15px rgba(0,0,0,.05);}
.grid{display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-top:20px;}
@media(max-width:768px){
.sidebar{display:none;}
.main{margin-left:0;}
.grid{grid-template-columns:1fr;}
}
</style>
</head>

<body>

<div class="sidebar">
<h2>🐾 ARSS</h2>
<a href="dashboard.php">Dashboard</a>
<a href="reports.php">Reports</a>
<a href="surrenders.php">Surrenders</a>
<a href="history.php">Activity Logs</a>
<a href="events.php">Events</a>
<a href="analytics.php" class="active">Analytics</a>
<a href="logout.php">Logout</a>
</div>

<div class="main">

<h1>Analytics Dashboard</h1>

<div class="stats">
<div class="card">
<h3>Total Reports</h3>
<h2><?= $totalReports ?></h2>
</div>

<div class="card">
<h3>Total Surrenders</h3>
<h2><?= $totalSurrenders ?></h2>
</div>

<div class="card">
<h3>Total Events</h3>
<h2><?= $totalEvents ?></h2>
</div>
</div>

<div class="grid">
<div class="card">
<h3>Reports Analytics</h3>
<canvas id="reportChart"></canvas>
</div>

<div class="card">
<h3>System Insights</h3>
<p>Monitor system activity and case trends.</p>
</div>
</div>

</div>

<script>
new Chart(reportChart,{
type:'bar',
data:{
labels:['Reports','Surrenders','Events'],
datasets:[{
label:'System Data',
data:[<?= $totalReports ?>,<?= $totalSurrenders ?>,<?= $totalEvents ?>]
}]
}
});
</script>

</body>
</html>
