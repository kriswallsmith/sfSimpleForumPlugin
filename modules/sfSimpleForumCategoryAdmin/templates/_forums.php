<ul>
<?php foreach ($sf_simple_forum_category->getFora() as $forum): ?>
  <li><?php echo link_to($forum->getName(), 'sfSimpleForumForumAdmin/edit?id='.$forum->getId()) ?></li>
<?php endforeach ?>
</ul>