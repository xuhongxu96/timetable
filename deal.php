<?php 
require_once 'db.php';
$id = $_POST['id'];
$name = $_POST['name'];
$event = $_POST['event'];
$time = isset($_POST['time']) ? $_POST['time'] : [];

$mysqli->set_charset("utf8");
$mysqli->query("SET NAMES 'utf8'");
$mysqli->query("SET CHARACTER SET UTF8");

if ($mysqli->connect_errno) {
    echo "未能连接到数据库 (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	die;
}

$id = $mysqli->real_escape_string($id);
$name = $mysqli->real_escape_string($name);
$event = $mysqli->real_escape_string($event);

if ($res = $mysqli->query("SELECT ID FROM students WHERE stu_id = '" . $id . "'")) {
	if ($res->num_rows) {
		$row = $res->fetch_assoc();
		$stuid = $row['ID'];
	} else {
		if (!$mysqli->query("INSERT INTO students(stu_id, name) VALUES ('" . $id . "', '" . $name . "')")) {
			echo "签到失败！";
			die;
		}
		$stuid = $mysqli->insert_id;
	}
} else {
	echo "查询失败！";
	die;
}

if (!$mysqli->query("DELETE FROM time WHERE student_id = $stuid")) {
	echo "初始化课表失败！" . $mysqli->error;
	die;
}

if (!($stmt = $mysqli->prepare("INSERT INTO time(student_id, day, item, event) VALUES ($stuid, ?, ?, '$event')"))) {
	echo "准备课表保存失败！" . $mysqli->error;
	die;
}

foreach($time as $t) {
	list($item, $day) = split(',', $t);
	$stmt->bind_param('ss', $day, $item);
	if (!$stmt->execute()) {
		echo "课表保存失败！" . $mysqli->error;
		die;
	}
}
$stmt->close();
echo "成功！";
$mysqli->close();
