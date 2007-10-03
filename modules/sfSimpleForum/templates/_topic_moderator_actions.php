<?php echo use_helper('I18N') ?>
<?php if ($user_is_moderator): ?>
  <ul class="post_actions">
    <li><?php echo link_to(__('Delete'), 'sfSimpleForum/deleteTopic?id='.$topic->getId(), array('confirm' =>__('Are you sure you want to delete this topic?'))) ?></li>
  </ul>
<?php endif ?>