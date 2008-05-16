<?php use_helper('I18N', 'sfSimpleForum') ?>

<?php if (sfConfig::get('app_sfSimpleForum_include_breadcrumb', true)): ?>
<?php slot('forum_navigation') ?>
  <?php echo forum_breadcrumb(array(
    sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums')
  )) ?>
<?php end_slot() ?>
<?php endif; ?> 

<div class="sfSimpleForum">
  
  <h1><?php echo __(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums')) ?></h1>
  
  <?php if (sfConfig::get('app_sfSimpleForumPlugin_allow_new_topic_outside_forum', true)): ?>
  <ul class="forum_actions">
    <li><?php echo link_to(__('New topic', null, 'sfSimpleForum'), 'sfSimpleForum/createTopic') ?></li>
  </ul>    
  <?php endif; ?>
  
  <?php include_partial('sfSimpleForum/figures', array(
    'display_topic_link'  => true,
    'nb_topics'           => $nb_topics,
    'topic_rule'          => 'sfSimpleForum/latestTopics',
    'display_post_link'   => true,
    'nb_posts'            => $nb_posts,
    'post_rule'           => 'sfSimpleForum/latestPosts',
    'feed_rule'           => 'sfSimpleForum/latestPostsFeed',
    'feed_title'          => $feed_title
  )) ?>
  
  <?php $category = '' ?>
  <table id="fora">
    <tr>
      <th class="forum_name"><?php echo __('Forum', null, 'sfSimpleForum') ?></th>
      <th class="forum_threads"><?php echo __('Topics', null, 'sfSimpleForum') ?></th>
      <th class="forum_posts"><?php echo __('Messages', null, 'sfSimpleForum') ?></th>
      <th class="forum_recent"><?php echo __('Last Message', null, 'sfSimpleForum') ?></th>
    </tr>
    <?php foreach ($forums as $forum): ?>
      <?php $new_category = $forum->getsfSimpleForumCategory()->getName() ?>
      <?php if ($new_category != $category && sfConfig::get('app_sfSimpleForumPlugin_display_categories', true)): $category = $new_category ?>
        <tr class="category">
          <td class="category_header" colspan="4"><?php echo $category ?></td>
        </tr>        
      <?php endif ?>
      <?php include_partial('sfSimpleForum/forum', array('forum' => $forum)) ?>
    <?php endforeach; ?>
  </table>

</div>
