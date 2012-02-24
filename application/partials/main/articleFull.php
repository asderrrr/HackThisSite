<h3 style="padding-bottom: 0px;margin-bottom: 0px;"><?php echo $title; ?></h3>
<sub>
<?php if (empty($revision)): ?>
Posted by: <?php echo (is_string($user) ? $user : $user['username']); ?> on <?php echo Date::dayFormat($date); ?>
<?php if (CheckAcl::can('editArticle')): ?>&nbsp;-&nbsp;<a href="<?php echo Url::format('/article/edit/' . $_id); ?>">Edit</a><?php endif; ?>
<?php if (CheckAcl::can('deleteArticle')): ?>&nbsp;-&nbsp;<a href="<?php echo Url::format('/article/delete/' . $_id); ?>">Delete</a><?php endif; ?>
<?php if (CheckAcl::can('viewArticleRevisions')): ?>&nbsp;-&nbsp;<a href="<?php echo Url::format('/article/revisions/' . $_id); ?>">Revisions</a><?php endif; ?>
<?php else: ?>
Replaced on <?php echo Date::dayFormat($_id->getTimestamp()); ?>
<?php endif; ?>
</sub>
<p><?php echo BBCode::parse(wordwrap($body, 100, "\n", true), '#'); ?></p>
<hr />
<?php if (!empty($mlt)): ?>
<b><u>More Like This:</u></b><br />
<?php
foreach ($mlt as $fetched) {
    echo '<a href="' . Url::format('article/view/' . Id::create($fetched, 'news')) . '">' . $fetched['title'] . '</a><br />';
}
?>
<hr />
<?php endif; ?>