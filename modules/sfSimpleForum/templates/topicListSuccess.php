<?php use_helper('I18N', 'Pagination', 'sfSimpleForum') ?>

<?php if (sfConfig::get('app_sfSimpleForum_include_breadcrumb', true)): ?>
<?php slot('forum_navigation') ?>
  <?php echo forum_breadcrumb($sf_data->getRaw('breadcrumb')) ?>
<?php end_slot() ?>
<?php endif; ?>

<?php if(sfConfig::get('app_sfSimpleForumPlugin_use_feeds', true)): ?>
<?php slot('auto_discovery_link_tag') ?>
  <?php echo auto_discovery_link_tag('rss', $feed_rule, array('title' => $feed_title)) ?>
<?php end_slot() ?>
<?php endif; ?>

<div class="sfSimpleForum">
  
  <h1><?php echo $title ?></h1>

  <?php if (sfConfig::get('app_sfSimpleForumPlugin_allow_new_topic_outside_forum', true)): ?>
  <ul class="forum_actions">
    <li><?php echo link_to(__('New topic'), 'sfSimpleForum/createTopic') ?></li>
  </ul>    
  <?php endif; ?>
    
  <div class="forum_figures">
    <?php echo format_number_choice('[0]No topic yet|[1]One topic|(1,+Inf]%topics% topics', array('%topics%' => $topics_pager->getNbResults()), $topics_pager->getNbResults()) ?>, 
    <?php echo link_to(
      format_number_choice('[0]No message|[1]One message|(1,+Inf]%posts% messages', array('%posts%' => $nb_posts), $nb_posts),
      $post_rule
     ) ?>
    <?php if(sfConfig::get('app_sfSimpleForumPlugin_use_feeds', true)): ?>
      <?php echo link_to(image_tag('/sfSimpleForumPlugin/images/feed-icon.png', 'align=top'), $feed_rule, 'title='.$feed_title) ?>
    <?php endif; ?>     
  </div>
  
  <?php include_partial('sfSimpleForum/topic_list', array('topics' => $topics_pager->getResults(), 'include_forum' => true)) ?>
  
  <?php echo pager_navigation($topics_pager, 'sfSimpleForum/latestTopics') ?>

</div>