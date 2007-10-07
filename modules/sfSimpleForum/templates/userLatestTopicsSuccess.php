<?php use_helper('I18N', 'Pagination', 'sfSimpleForum') ?>
<?php $title = __('Topics by %user%', array('%user%' => get_partial('sfSimpleForum/author_name', array('author' => $user, 'sf_cache_key' => $username)))) ?>

<?php if (sfConfig::get('app_sfSimpleForum_include_breadcrumb', true)): ?>
<?php slot('forum_navigation') ?>
  <?php echo forum_breadcrumb(array(
    array(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'), 'sfSimpleForum/forumList'),
    $title
  )) ?>
<?php end_slot() ?>
<?php endif; ?>

<div class="sfSimpleForum">
  
  <h1><?php echo $title ?></h1>

  <?php if (sfConfig::get('app_sfSimpleForumPlugin_allow_new_topic_outside_forum', true)): ?>
  <ul class="forum_actions">
    <li><?php echo link_to(__('New topic'), 'sfSimpleForum/createTopic') ?></li>
  </ul>    
  <?php endif; ?>
  
  <?php include_partial('sfSimpleForum/figures', array(
    'display_topic_link'  => false,
    'nb_topics'           => $topics_pager->getNbResults(),
    'topic_rule'          => '',
    'display_post_link'   => true,
    'nb_posts'            => sfSimpleForumPostPeer::countForUser($user->getId()),
    'post_rule'           => 'sfSimpleForum/userLatestPosts?username='.$username,
    'feed_rule'           => 'sfSimpleForum/userLatestTopicsFeed?username='.$username,
    'feed_title'          => $feed_title
  )) ?>
    
  <?php include_partial('sfSimpleForum/topic_list', array('topics' => $topics_pager->getResults(), 'include_forum' => true)) ?>
  
  <?php echo pager_navigation($topics_pager, 'sfSimpleForum/userLatestTopics?username='.$username) ?>

</div>