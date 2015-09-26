<?php 
require_once 'db.php';
$event = $_POST['event'];

$mysqli->set_charset("utf8");
$mysqli->query("SET NAMES 'utf8'");
$mysqli->query("SET CHARACTER SET UTF8");

function finish($ret) {
	echo "{\"error\": \"$ret\"}";
	die;
}
if ($mysqli->connect_errno) {
     $ret = "未能连接到数据库 (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	 finish($ret);
}

$event = $mysqli->real_escape_string($event);

$students = array();

if ($res = $mysqli->query("SELECT DISTINCT(name) FROM time LEFT JOIN students ON students.ID = time.student_id WHERE event = '$event'")) {
	while ($row = $res->fetch_assoc()) {
		$students[$row['name']] = true;
	}
} else {
	finish("查询失败！");
}

$map = array();
for ($i = 1; $i <= 8; ++$i) {
	$temp = array();
	for ($j = 1; $j <= 13; ++$j) {
		$temp[] = $students;
	}
	$map[] = $temp;
}

if ($res = $mysqli->query("SELECT * FROM time LEFT JOIN students ON students.ID = time.student_id WHERE event = '$event'")) {
	while ($row = $res->fetch_assoc()) {
		$map[$row['day']][$row['item']][$row['name']] = false;
	}
} else {
	finish("查询失败！");
}

echo json_encode($map);

$mysqli->close();
