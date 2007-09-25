<?php use_helper('I18N', 'Pagination', 'sfSimpleForum', 'Validation') ?>

<?php if (sfConfig::get('app_sfSimpleForum_include_breadcrumb', true)): ?>
<?php slot('forum_navigation') ?>
  <?php echo forum_breadcrumb($sf_data->getRaw('breadcrumb')) ?>
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
        $topic->getIsSticked() ? __('Unstick') : __('Stick'), 
        'sfSimpleForum/toggleStick?id='.$topic->getId()
      ) ?></li>
      <li><?php echo link_to(
        $topic->getIsLocked() ? __('Unlock') : __('Lock'), 
        'sfSimpleForum/toggleLock?id='.$topic->getId()
      ) ?></li>
    <?php endif ?>
  </ul>
    
  <div class="forum_figures">
    <?php echo format_number_choice('[1]1 message, no reply|(1,+Inf]%posts% messages', array('%posts%' => $post_pager->getNbResults()), $post_pager->getNbResults()) ?> 
    <?php if (sfConfig::get('app_sfSimpleForumPlugin_count_views', true)): ?>
    - <?php echo format_number_choice('[1]1 view|(1,+Inf]%views% views', array('%views%' => $topic->getNbViews()), $topic->getNbViews()) ?>
    <?php endif; ?>
    <?php if(sfConfig::get('app_sfSimpleForumPlugin_use_feeds', true)): ?>
      <?php echo link_to(image_tag('/sfSimpleForumPlugin/images/feed-icon.png', 'align=top'), 'sfSimpleForum/topicFeed?id='.$topic->getId().'&stripped_title='.$topic->getStrippedTitle(), 'title='.$feed_title) ?>
    <?php endif; ?>    
  </div>
  
  <table id="messages">
    <?php foreach ($posts as $post): ?>
      <?php include_partial('sfSimpleForum/post', array('post' => $post, 'include_thread' => false)) ?>
    <?php endforeach; ?>
  </table>
  
  <?php echo pager_navigation($post_pager, 'sfSimpleForum/topic?id='.$topic->getId().'&stripped_title='.$topic->getStrippedTitle()) ?>
  <?php if (!$topic->getIsLocked() && $sf_user->isAuthenticated()): ?>
  <h2>
    <?php echo __('Post a reply') ?>
  </h2>

  <?php echo form_tag('sfSimpleForum/addPost', 'id=add_topic name=add_topic') ?>
    <?php echo input_hidden_tag('topic_id', $topic->getId()) ?>
  
    <?php echo form_error('body') ?>
    <?php echo label_for('body', __('Body')) ?>
    <?php echo textarea_tag('body', '', 'id=topic_body') ?>

    <?php echo submit_tag(__('Post'), 'id=topic_submit') ?>
  </form>
  <?php elseif (!$topic->getIsLocked() && !$sf_user->isAuthenticated()): ?>
    <ul class="forum_actions">
        <li><?php echo link_to(
          __('Post a reply'), 
          sfConfig::get('sf_login_module').'/'.sfConfig::get('sf_login_action')
        ) ?></li>
    </ul>
  <?php endif; ?>
</div>