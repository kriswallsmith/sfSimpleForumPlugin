<?php use_helper('I18N') ?>
<?php $author = sfGuardUserPeer::retrieveByUsername($author_name) ?>
<?php $nb_posts = $author->countsfSimpleForumPosts() ?>
<?php if ($author->hasPermission('moderator')): ?>
  <?php echo __('Moderator') ?><br/>
<?php endif ?>
<?php echo format_number_choice('[1]1 message|(1,+Inf] %1% messages', array('%1%' => $nb_posts), $nb_posts) ?><br />