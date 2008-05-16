<?php use_helper('I18N', 'Pagination', 'sfSimpleForum', 'Validation') ?>

<?php if (sfConfig::get('app_sfSimpleForum_include_breadcrumb', true)): ?>
<?php slot('forum_navigation') ?>
  <?php echo forum_breadcrumb(array(
    array(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'), 'sfSimpleForum/forumList'),
    array($topic->getsfSimpleForumForum()->getName(), 'sfSimpleForum/forum?forum_name='.$topic->getsfSimpleForumForum()->getStrippedName()),
    $topic->getTitle()
  )) ?>
<?php end_slot() ?>
<?php endif; ?>

<?php if(sfConfig::get('app_sfSimpleForumPlugin_use_feeds', true)): ?>
<?php slot('auto_discovery_link_tag') ?>
  <?php echo auto_discovery_link_tag('rss', 'sfSimpleForum/topicFeed?id='.$topic->getId().'&stripped_title='.$topic->getStrippedTitle(), array('title' => $feed_title)) ?>
<?php end_slot() ?>
<?php endif; ?>

<div class="sfSimpleForum">
  
  <h1><?php echo $topic->getTitle() ?></h1>

  <ul class="forum_actions">
    <?php if ($sf_user->hasCredential('moderator')): ?>
      <li><?php echo link_to(
        $topic->getIsSticked() ? __('Unstick', null, 'sfSimpleForum') : __('Stick', null, 'sfSimpleForum'), 
        'sfSimpleForum/toggleStick?id='.$topic->getId()
      ) ?></li>
      <li><?php echo link_to(
        $topic->getIsLocked() ? __('Unlock', null, 'sfSimpleForum') : __('Lock', null, 'sfSimpleForum'), 
        'sfSimpleForum/toggleLock?id='.$topic->getId()
      ) ?></li>
    <?php endif ?>
  </ul>
    
  <div class="forum_figures">
    <?php echo format_number_choice('[1]1 message, no reply|(1,+Inf]%posts% messages', array('%posts%' => $post_pager->getNbResults()), $post_pager->getNbResults(), 'sfSimpleForum') ?> 
    <?php if (sfConfig::get('app_sfSimpleForumPlugin_count_views', true)): ?>
    - <?php echo format_number_choice('[0,1]1 view|(1,+Inf]%views% views', array('%views%' => $topic->getNbViews()), $topic->getNbViews(), 'sfSimpleForum') ?>
    <?php endif; ?>
    <?php if(sfConfig::get('app_sfSimpleForumPlugin_use_feeds', true)): ?>
      <?php echo link_to(image_tag('/sfSimpleForumPlugin/images/feed-icon.png', 'align=top'), 'sfSimpleForum/topicFeed?id='.$topic->getId().'&stripped_title='.$topic->getStrippedTitle(), 'title='.$feed_title) ?>
    <?php endif; ?>    
  </div>
  
  <?php include_partial('sfSimpleForum/post_list', array('posts' => $post_pager->getResults(), 'include_topic' => false)) ?>
  
  <?php echo pager_navigation($post_pager, 'sfSimpleForum/topic?id='.$topic->getId().'&stripped_title='.$topic->getStrippedTitle()) ?>
  
  <?php if (!$topic->getIsLocked() && $sf_user->isAuthenticated()): ?>
    
    <h2>
      <?php echo __('Post a reply', null, 'sfSimpleForum') ?>
    </h2>
    <?php include_partial('sfSimpleForum/add_post_form', array('topic' => $topic)) ?>
      
  <?php elseif (!$topic->getIsLocked() && !$sf_user->isAuthenticated()): ?>
    
    <ul class="forum_actions">
        <li><?php echo link_to(
          __('Post a reply', null, 'sfSimpleForum'), 
          sfConfig::get('sf_login_module').'/'.sfConfig::get('sf_login_action')
        ) ?></li>
    </ul>
    
  <?php elseif ($topic->getIsLocked() && $sf_user->isAuthenticated()): ?>
    
    <?php echo __('This topic was locked by a forum moderator. No reply can be added.', null, 'sfSimpleForum') ?>
    
  <?php endif; ?>
</div>