<?php use_helper('I18N', 'Pagination', 'sfSimpleForum') ?>

<?php if (sfConfig::get('app_sfSimpleForum_include_breadcrumb', true)): ?>
<?php slot('forum_navigation') ?>
  <?php echo forum_breadcrumb($sf_data->getRaw('breadcrumb')) ?>
<?php end_slot() ?>
<?php endif; ?>

<?php if(sfConfig::get('app_sfSimpleForumPlugin_use_feeds', true)): ?>
<?php slot('auto_discovery_link_tag') ?>
  <?php echo auto_discovery_link_tag('rss', 'sfSimpleForum/latestForumPostsFeed?forum_name='.$forum->getStrippedName(), array('title' => $feed_title)) ?>
<?php end_slot() ?>
<?php endif; ?>

<div class="sfSimpleForum">
  
  <h1><?php echo $forum->getName() ?></h1>

  <ul class="forum_actions">
    <li><?php echo link_to(__('New topic'), 'sfSimpleForum/createTopic?forum_name='.$forum->getStrippedName()) ?></li>
  </ul>
    
  <div class="forum_figures">
    <?php echo format_number_choice('[0]No topic yet|[1]One topic|(1,+Inf]%threads% topics', array('%threads%' => $forum->getNbTopics()), $forum->getNbTopics()) ?>, 
    <?php echo link_to(
      format_number_choice('[0]No message|[1]One message|(1,+Inf]%posts% messages', array('%posts%' => $forum->getNbPosts()), $forum->getNbPosts()),
      'sfSimpleForum/latestForumPosts?forum_name='.$forum->getStrippedName()
     ) ?>
    <?php if(sfConfig::get('app_sfSimpleForumPlugin_use_feeds', true)): ?>
      <?php echo link_to(image_tag('/sfSimpleForumPlugin/images/feed-icon.png', 'align=top'), 'sfSimpleForum/latestForumPostsFeed?forum_name='.$forum->getStrippedName(), 'title='.$feed_title) ?>
    <?php endif; ?>     
  </div>
  
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
      <?php include_partial('sfSimpleForum/topic', array('topic' => $topic)) ?>
    <?php endforeach; ?>
  </table>
  
  <?php echo pager_navigation($topic_pager, 'sfSimpleForum/forum?forum_name='.$forum->getStrippedName()) ?>

</div>