<?php  
session_start();
if (empty($_SESSION['uname'])) {

	$project = file_get_contents("data.txt");
	$project_json = json_decode($project);
	$project_json->canWrite = false;
	$project_json->canWriteOnParent = false;

	$returnArray = array(
		'ok' => true, 
		'project' => $project_json, 
		'hash' => md5($project), 
		'message' => '', 
		'errorMessages' => '',
		'uname' => "未登录用户"
	);
	echo json_encode($returnArray);


} elseif (!empty($_POST['CM']) && !empty($_POST['prj']) && !empty($_POST['hash']) && $_POST['CM'] == 'SVPROJECT') {
	
	$hash = md5(file_get_contents("data.txt"));

	$project = json_decode($_POST['prj']);
		
	if (!isset($project->resources)) {
		$project->resources = array(
			'id' => 'tmp_1',
			'name' => 'Resource 1'
		);
	}

	if (!isset($project->roles)) {
		$project->roles = array(
			'id' => 'tmp_1',
			'name' => 'Developer'
		);
	}

	$project->lastModify = $_SESSION['uname'];
	
	if ($hash == $_POST['hash']) {

		file_put_contents("data.txt", json_encode($project));
		file_put_contents("data" . date("Ymd") . ".txt", json_encode($project));
		$returnOk = true;
		$errorMessages = array();
		$message = '';
	} else {
		$returnOk = false;
		$errorMessages = array();
		$message = '版本已变更，请刷新后重试！';
	}

	$returnArray = array(
		'ok' => $returnOk, 
		'project' => $project, 
		'hash' => md5(json_encode($project)),
		'message' => $message, 
		'errorMessages' => $errorMessages
	);

	echo json_encode($returnArray);
} elseif (!empty($_GET['CM']) && $_GET['CM'] == 'GETHASH') {
	$project = file_get_contents("data.txt");
	$objProject = json_decode($project);
	$returnArray = array(
		'ok' => true, 
		'hash' => md5($project), 
		'message' => '', 
		'errorMessages' => '',
		'lastModify' => $objProject->lastModify
	);
	echo json_encode($returnArray);
} elseif (!empty($_GET['CM']) && $_GET['CM'] == 'BACKUP') {
	$project = file_get_contents("data.txt");
	$backupProject = file_get_contents("backupdata.txt");
	$projectJson = json_decode($project);
	$backupProjectJson = json_decode($backupProject);
	$fromTime =  strtotime("-20 day") * 1000;


	$bcount = 0;
    foreach ($backupProjectJson->tasks AS $key => $task) {
		$bcount++;
	}
	echo "1:" . count($projectJson->tasks) . " b:" . $bcount;
    
	$parentIds = array(1 => 0 , 2 => 0 , 3 => 0);

	foreach ($projectJson->tasks AS $key => $task) {

		if ($task->level > 1 && $task->end < $fromTime) {
			$task->parentId = $parentIds[$task->level-1];
			$backupProjectJson->tasks->{$task->id} = $task;
			echo $task->id;
			unset($projectJson->tasks[$key]);
		}
		
		$parentIds[$task->level] = $task->id;

	}
	
	$projectJson->tasks = array_values($projectJson->tasks);


	file_put_contents("data.txt", json_encode($projectJson));
	file_put_contents("backupdata.txt", json_encode($backupProjectJson));

	echo "DONE";
}  elseif (!empty($_GET['CM']) && $_GET['CM'] == 'REVERSE') {
	$project = file_get_contents("data.txt");
	$backupProject = file_get_contents("backupdata.txt");
	$projectJson = json_decode($project);
	$backupProjectJson = json_decode($backupProject);

	foreach ($backupProjectJson->tasks AS $key => $task) {

		foreach ($projectJson->tasks AS $skey => $stask) {


			if ($stask->id == $task->parentId) {
				unset($task->parentId);
				array_splice($projectJson->tasks, $skey+1, 0, array($task));
				unset($backupProjectJson->tasks->{$key});
				break;
			}

		}

	}

	$bcount = 0;
    foreach ($backupProjectJson->tasks AS $key => $task) {
		$bcount++;
	}
	echo "1:" . count($projectJson->tasks) . " b:" . $bcount;

	file_put_contents("data.txt", json_encode($projectJson));
	file_put_contents("backupdata.txt", json_encode($backupProjectJson));


	echo "DONE";
} else {
	$project = file_get_contents("data.txt");
	$returnArray = array(
		'ok' => true, 
		'project' => json_decode($project), 
		'hash' => md5($project), 
		'message' => '', 
		'errorMessages' => '',
		'uname' => $_SESSION['uname']
	);
	echo json_encode($returnArray);
}
 
?> 