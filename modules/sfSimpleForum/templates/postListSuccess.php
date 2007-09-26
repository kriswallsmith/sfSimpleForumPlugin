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
  
  <div class="forum_figures">
    <?php echo link_to_if(
      $nb_topics,
      format_number_choice('[0]No topic yet|[1]One topic|(1,+Inf]%topics% topics', array('%topics%' => $nb_topics), $nb_topics),
      $topic_rule
      ) ?>, 
    <?php echo format_number_choice('[0]No message|[1]One message|(1,+Inf]%posts% messages', array('%posts%' => $post_pager->getNbResults()), $post_pager->getNbResults()) ?>
    <?php if(sfConfig::get('app_sfSimpleForumPlugin_use_feeds', true)): ?>
      <?php echo link_to(image_tag('/sfSimpleForumPlugin/images/feed-icon.png', 'align=top'), $feed_rule, 'title='.$feed_title) ?>
    <?php endif; ?>  
  </div>
  
  <table id="messages">
    <?php foreach ($post_pager->getResults() as $post): ?>
      <?php include_partial('sfSimpleForum/post', array('post' => $post, 'include_topic' => true)) ?>
    <?php endforeach; ?>
  </table>
  
  <?php echo pager_navigation($post_pager, $rule) ?>

</div>