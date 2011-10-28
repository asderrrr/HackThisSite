<?php
if (!empty($news)) {
	if ($multiple) {
		$first = true;
		foreach ($news as $post) {
			if (!$first) {
				echo '<br/ ><hr /><br />';
			}
			
			echo $template->showNews($post);
			$first = false;
		}
	} else {
		echo $template->showNews($news[0]);
		
		if ($news[0]['commentable']) {
			echo new Widget('comment', array('id' => $news[0]['_id'], 'page' => 1));
		}
	}
}
?>