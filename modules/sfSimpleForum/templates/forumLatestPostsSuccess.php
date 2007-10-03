<?php use_helper('I18N', 'Pagination', 'sfSimpleForum') ?>
<?php $title = __('Latest messages') ?>

<?php if (sfConfig::get('app_sfSimpleForum_include_breadcrumb', true)): ?>
<?php slot('forum_navigation') ?>
  <?php echo forum_breadcrumb(array(
    array(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'), 'sfSimpleForum/forumList'),
    array($forum->getName(), 'sfSimpleForum/forum?forum_name='.$name),
    $title
  )) ?>  
<?php end_slot() ?>
<?php endif; ?>

<div class="sfSimpleForum">
  
  <h1><?php echo $title ?></h1>
  
  <?php include_partial('sfSimpleForum/figures', array(
    'display_topic_link'  => true,
    'nb_topics'           => $forum->countsfSimpleForumTopics(),
    'topic_rule'          => 'sfSimpleForum/forum?forum_name='.$name,
    'display_post_link'   => false,
    'nb_posts'            => $post_pager->getNbResults(),
    'post_rule'           => '',
    'feed_rule'           => 'sfSimpleForum/forumLatestPostsFeed?forum_name='.$name,
    'feed_title'          => $feed_title
  )) ?>
  
  <?php include_partial('sfSimpleForum/post_list', array('posts' => $post_pager->getResults(), 'include_topic' => true)) ?>
  
  <?php echo pager_navigation($post_pager, 'sfSimpleForum/forumLatestPosts?forum_name='.$name) ?>

</div>