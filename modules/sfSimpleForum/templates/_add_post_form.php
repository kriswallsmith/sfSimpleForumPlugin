<?php echo form_tag('sfSimpleForum/add' . (isset($topic) ? 'Post' : 'Topic'), 'id=add_topic name=add_topic') ?>

  <?php if (isset($topic)): ?>
    
    <?php echo input_hidden_tag('topic_id', $topic->getId()) ?>
    
  <?php else: ?>
    
    <?php echo form_error('title') ?>
    <?php echo label_for('title', __('Title')) ?>
    <?php echo input_tag('title', '', 'id=topic_title') ?>
    <?php if (isset($forum)): ?>
      <?php echo input_hidden_tag('forum_name', $forum->getStrippedName()) ?>
    <?php else: ?>
      <?php echo label_for('forum', __('Forum')) ?>
      <?php echo select_tag('forum_name', options_for_select(sfSimpleForumForumPeer::getAllAsArray())) ?>
    <?php endif; ?>
    
  <?php endif; ?>
  
  <?php echo form_error('body') ?>
  <?php echo label_for('body', __('Body')) ?>
  <?php echo textarea_tag('body', '', 'id=topic_body') ?>
  
  <?php if (!isset($topic) && $sf_user->hasCredential('moderator')): ?>
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