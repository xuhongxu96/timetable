<?php 
$id = $_POST['id'];
$name = $_POST['name'];
$event = $_POST['event'];

//$mysqli = new mysqli("localhost", "root", "", "timetable");
$mysqli = new mysqli("qdm121543438.my3w.com", "qdm121543438", "Xuhongxu96", "qdm121543438_db");
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

$id = $mysqli->real_escape_string($id);
$name = $mysqli->real_escape_string($name);

if ($res = $mysqli->query("SELECT ID FROM students WHERE stu_id = '$id' AND name = '$name'")) {
	if ($res->num_rows) {
		$row = $res->fetch_assoc();
		$stuid = $row['ID'];
	} else {
		finish("查无此人");
	}
} else {
	finish("查询失败！");
}

if ($res = $mysqli->query("SELECT * FROM time WHERE student_id = $stuid AND event = '$event'")) {
	echo "[";
	$i = false;
	while ($row = $res->fetch_assoc()) {
		if ($i) echo ",";
		$i = true;
        printf('{"day": "%s", "item": "%s"}', $row['day'], $row['item']);
    }
	echo "]";
} else {
	finish("查询失败");
}


$mysqli->close();
