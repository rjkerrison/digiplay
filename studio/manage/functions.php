<?php
Output::set_template();

switch($_REQUEST["action"]) {
	case "now-next":
		$json = file_get_contents(Config::get_param("now-next-api"));
		$json = json_decode($json);
		$return = "<div class=\"col-sm-6 navbar-brand\">On now: <span id=\"on-now\">".$json[0]->name."</span></div>
			<div class=\"col-sm-6 navbar-brand\">On next: <span id=\"on-next\">".$json[1]->name."</span></div>";
		echo $return;
		break;
	case "info-content":
		echo(Config::get_param("info-content"));
		break;
	case "search":
		$index = array();
		$query = $_REQUEST["search-text"];
		if ($_REQUEST["search-title"] == "on") $index[] = "title";
		if ($_REQUEST["search-artist"] == "on") $index[] = "artist";
		if ($_REQUEST["search-album"] == "on") $index[] = "album";
		$index = implode($index," ");

		if($query) $search = Search::tracks($query,$index,50);

		if($search["results"]) {
			$return = "<table class=\"table table-striped table-hover\" cellspacing=\"0\">
				<thead>
					<tr>
						<th class=\"icon\"></th>
						<th class=\"artist\">Artist</th>
						<th class=\"title\">Title</th>
						<th class=\"album\">Album</th>
						<th class=\"length\">Length</th>
					</tr>
				</thead>
				<tbody>";
			foreach($search["results"] as $track) {
				$track = Tracks::get($track);
				$return .= "<tr id=\"".$track->get_id()."\">
					<td class=\"icon\">".Bootstrap::glyphicon("music")."</td>
					<td class=\"artist nowrap\">".$track->get_artists_str()."</td>
					<td class=\"title nowrap\">".$track->get_title()."</td>
					<td class=\"album nowrap\">".$track->get_album()->get_name()."</td>
					<td class=\"length nowrap\">".Time::format_succinct($track->get_length())."</td>
				</tr>";
			}
			$return .= "</tbody></table>";
			if($search["total"] > 50) $return .= "<span class=\"result-limit\">Only showing top 50 results out of ".$search["total"]." total.  Try a more specific search.</span>";
			echo($return);
		} else {
			echo("<h3>No results found, or your search term was too generic.  <br />Try a different search query.</h3>");
		}
		break;
	case "messages":
		$emails = Emails::get(NULL,NULL,NULL,25,NULL);
		$return = "<table class=\"table table-striped table-hover\">
			<thead>
				<tr>
					<th class=\"icon\"></th>
					<th class=\"from\">From</th>
					<th class=\"subject\">Subject</th>
					<th class=\"datetime\">Date/Time</th>
				</tr>
			</thead>
			<tbody>";
		foreach($emails as $email) {
			$return .= "<tr id=\"".$email->get_id()."\">
				<td class=\"icon\">".($email->get_new_flag()? Bootstrap::glyphicon("envelope") : "")."</td>
				<td class=\"from nowrap\">".$email->get_sender()."</td>
				<td class=\"subject nowrap\">".$email->get_subject()."</td>
				<td class=\"datetime nowrap\">".date("d/m/y H:i", $email->get_datetime())."</td>
			</tr>";
		}
		$return .= "</tbody></table>";
		echo($return);
		break;
	case "message":
		$message = Emails::get_by_id($_REQUEST['id']);
		echo($message->get_body_formatted());
		$message->mark_as_read();
		break;
}
?>