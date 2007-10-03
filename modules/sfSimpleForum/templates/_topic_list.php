<table id="threads">
  <tr>
    <th class="thread_name"><?php echo __('Topic') ?></th>
    <th class="thread_replies"><?php echo __('Replies') ?></th>
    <?php if (sfConfig::get('app_sfSimpleForumPlugin_count_views', true)): ?>
    <th class="thread_replies"><?php echo __('Views') ?></th>
    <?php endif; ?>
    <th class="thread_recent"><?php echo __('Last Message') ?></th>
  </tr>
  <?php foreach ($topics as $topic): ?>
    <?php include_partial('sfSimpleForum/topic', array(
      'topic'             => $topic, 
      'include_forum'     => $include_forum, 
      'user_is_moderator' => $sf_user->hasCredential('moderator'),
      'sf_cache_key'      => $topic->getId().'_'.$sf_user->hasCredential('moderator')
      )) ?>
  <?php endforeach; ?>
</table>