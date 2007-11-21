<?php use_helper('I18N') ?>
<?php $author = sfSimpleForumTools::getUserByUsername($author_name) ?>
<?php $nb_posts = $author->countsfSimpleForumPosts() ?>
<?php echo link_to(get_partial('sfSimpleForum/author_name', array('author' => $author, 'sf_cache_key' => $author_name)), 'sfSimpleForum/userLatestPosts?username='.$author_name) ?><br/>
<?php if ($author->hasPermission('moderator')): ?>
  <?php echo __('Moderator') ?><br/>
<?php endif ?>
<?php echo format_number_choice('[1]1 message|(1,+Inf] %1% messages', array('%1%' => $nb_posts), $nb_posts) ?><br />