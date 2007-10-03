<?php use_helper('I18N', 'Validation', 'sfSimpleForum') ?>

<?php if (sfConfig::get('app_sfSimpleForum_include_breadcrumb', true)): ?>
<?php slot('forum_navigation') ?>
<?php if ($forum): ?>
  <?php echo forum_breadcrumb(array(
    array(__(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums')), 'sfSimpleForum/forumList'),
    array($forum->getName(), 'sfSimpleForum/forum?forum_name='.$forum->getStrippedName()),
    __('New topic')
  )) ?>
<?php else: ?>
  <?php echo forum_breadcrumb(array(
    array(__(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums')), 'sfSimpleForum/forumList'),
    __('New topic')
  )) ?>
<?php endif; ?>
<?php end_slot() ?>
<?php endif; ?>

<div class="sfSimpleForum">

  <h1><?php echo __('Create a new topic') ?></h1>
  
  <?php include_partial('sfSimpleForum/add_post_form', array('forum' => $forum)) ?>

</div>