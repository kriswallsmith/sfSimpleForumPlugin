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

  <?php echo form_tag('sfSimpleForum/addTopic', 'id=add_topic name=add_topic') ?>
    <?php if ($forum): ?>
      <?php echo input_hidden_tag('forum_name', $forum->getStrippedName()) ?>
    <?php endif; ?>
    <?php echo input_hidden_tag('topic_id', $topic_id) ?>
    
    <?php echo form_error('title') ?>
    <?php echo label_for('title', __('Title')) ?>
    <?php echo input_tag('title', $topic_id ? __('Re: ') . $topic_name : '', 'id=topic_title') ?>
    
    <?php if (!$forum): ?>
      <?php echo label_for('forum', __('Forum')) ?>
      <?php echo select_tag('forum_name', options_for_select(sfSimpleForumForumPeer::getAllAsArray())) ?>
    <?php endif; ?>
    
    <?php echo form_error('body') ?>
    <?php echo label_for('body', __('Body')) ?>
    <?php echo textarea_tag('body', '', 'id=topic_body') ?>
    <?php if ($sf_user->hasCredential('moderator')): ?>
    <div class="option">
      <?php echo checkbox_tag('is_sticked', '1')?>
      <?php echo label_for('is_sticked', __('Sticked topic')) ?>
    </div>
    <div class="option">
      <?php echo checkbox_tag('is_locked', '1')?>
      <?php echo label_for('is_locked', __('Locked topic')) ?>
    </div>
    <?php endif; ?>
    <?php echo submit_tag(__('Post'), 'id=topic_submit') ?>
  </form>

</div>