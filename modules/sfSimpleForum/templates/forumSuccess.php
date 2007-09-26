<?php use_helper('I18N', 'Pagination', 'sfSimpleForum') ?>

<?php if (sfConfig::get('app_sfSimpleForum_include_breadcrumb', true)): ?>
<?php slot('forum_navigation') ?>
  <?php echo forum_breadcrumb(array(
    array(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'), 'sfSimpleForum/forumList'),
    $forum->getName()
  )) ?>
<?php end_slot() ?>
<?php endif; ?>

<div class="sfSimpleForum">
  
  <h1><?php echo $forum->getName() ?></h1>

  <ul class="forum_actions">
    <li><?php echo link_to(__('New topic'), 'sfSimpleForum/createTopic?forum_name='.$forum->getStrippedName()) ?></li>
  </ul>
  
  <?php include_partial('sfSimpleForum/figures', array(
    'display_topic_link'  => false,
    'nb_topics'           => $forum->getNbTopics(),
    'topic_rule'          => '',
    'display_post_link'   => true,
    'nb_posts'            => $forum->getNbPosts(),
    'post_rule'           => 'sfSimpleForum/forumLatestPosts?forum_name='.$forum->getStrippedName(),
    'feed_rule'           => 'sfSimpleForum/latestForumPostsFeed?forum_name='.$forum->getStrippedName(),
    'feed_title'          => $feed_title
  )) ?>
  
  <?php include_partial('sfSimpleForum/topic_list', array('topics' => $topics, 'include_forum' => false)) ?>
  
  <?php echo pager_navigation($topic_pager, 'sfSimpleForum/forum?forum_name='.$forum->getStrippedName()) ?>

</div>