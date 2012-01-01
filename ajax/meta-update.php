<?php
require_once("pre.php");
Output::set_template();

if(Session::is_group_user('music_admin')){
	/* update notes */
	$track = Tracks::get_by_id($_REQUEST["id"]);
	if($_REQUEST["notes"] != $track->get_notes()) {
		$track->set_notes($_REQUEST["notes"]);
		$result = $track->save_audio();
		if(!$result) exit("Error: Something is incorrect.  You may have discovered a bug!");
	}
	if($_REQUEST["new_keyword"]) $track->add_keywords($_REQUEST["new_keyword"]);
	exit("success");
} else {
	exit("Error: You do not have permission to modify this.");
}
?>