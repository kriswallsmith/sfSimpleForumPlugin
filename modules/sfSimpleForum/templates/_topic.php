<?php use_helper('Date') ?>
<tr>
  <td class="thread_name">
    
    <?php if ($topic->getIsSticked()): ?>
      <?php echo image_tag('/sfSimpleForumPlugin/images/note.png', array(
        'align' => 'absbottom',
        'alt'   => __('Sticked topic'),
        'title' => __('Sticked topic')
      )) ?>
    <?php endif; ?>
    <?php if ($topic->getIsLocked()): ?>
      <?php echo image_tag('/sfSimpleForumPlugin/images/lock.png', array(
        'align' => 'absbottom',
        'alt'   => __('Locked topic'),
        'title' => __('Locked topic')
      )) ?>
    <?php endif; ?>
    <?php if (!$topic->getIsLocked() && !$topic->getIsSticked()): ?>
      <?php $image = $topic->getNbReplies() ? 'comments' : 'comment'  ?>
      <?php echo image_tag('/sfSimpleForumPlugin/images/'.$image.'.png', array(
        'align' => 'absbottom'
      )) ?>
    <?php endif; ?>
    
    <?php echo link_to(
      $topic->getTitle(),
      'sfSimpleForum/topic?id='.$topic->getId().'&stripped_title='.$topic->getStrippedTitle(),
      array('class' => $topic->getIsNew() ? 'new' : '')) ?>
      
    <?php $pages = ceil(($topic->getNbPosts()) / sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)) ?>
    <?php if ($pages > 1): ?>
      <?php echo link_to(
        '(last page)',
        'sfSimpleForum/topic?id='.$topic->getId().'&stripped_title='.$topic->getStrippedTitle().'&page='.$pages
      ) ?>
    <?php endif; ?>
    
    <?php if ($include_forum): ?>
      in <?php echo link_to(
        $topic->getsfSimpleForumForum()->getName(),
        'sfSimpleForum/forum?forum_name='.$topic->getsfSimpleForumForum()->getStrippedName()
      ) ?>
    <?php endif; ?>
    
    <?php include_partial('sfSimpleForum/topic_moderator_actions', array('topic' => $topic, 'user_is_moderator' => $user_is_moderator)) ?>
    
  </td>
  <td class="thread_replies"><?php echo $topic->getNbReplies() ?></td>

  <?php if (sfConfig::get('app_sfSimpleForumPlugin_count_views', true)): ?>
  <td class="thread_views"><?php echo $topic->getNbViews() ?></td>
  <?php endif; ?>

  <td class="thread_recent">
    <?php $message_link = $topic->getNbReplies() ? __('Last reply') : __('Posted') ?>
    <?php $latest_post = $topic->getsfSimpleForumPost() ?>
    <?php echo $message_link . ' ' . __('%date% ago by %author%', array(
      '%date%'   => distance_of_time_in_words($latest_post->getCreatedAt('U')),
      '%author%' => link_to(get_partial('sfSimpleForum/author_name', array('author' => $latest_post->getAuthorName(), 'sf_cache_key' => $latest_post->getAuthorName())), 'sfSimpleForum/userLatestPosts?username='.$latest_post->getAuthorName())
      )) ?>

    <?php if ($topic->getNbReplies()): ?>
      (<?php echo link_to(__('view'), 'sfSimpleForum/post?id='.$topic->getLatestPostId()) ?>)
    <?php endif; ?>

  </td>

</tr>