<?php use_helper('sfSimpleForum', 'Date') ?>
<tr>
  <td class="post_author">
    <?php echo link_to($post->getAuthorName(), 'sfSimpleForum/latestUserPosts?username='.$post->getAuthorName()) ?><br/>
    <?php if (sfConfig::get('app_sfSimpleForumPlugin_show_author_details', false)): ?>
      <?php echo include_partial('sfSimpleForum/author', array('author_name' => $post->getAuthorName())) ?>
    <?php endif; ?>
    <?php echo format_date($post->getCreatedAt('U')) ?>
  </td>
  <td class="post_message">
    <?php if ($include_thread): ?>
    <div class="post_details">
      <?php echo link_to($post->getsfSimpleForumForum()->getName(), 'sfSimpleForum/forum?forum_name='.$post->getsfSimpleForumForum()->getStrippedName()) ?>
     &raquo;
      <?php echo link_to($post->getsfSimpleForumTopic()->getTitle(), 'sfSimpleForum/post?id='.$post->getId()) ?>
      <?php endif ?>
    </div>
    <div class="post_content"><a name="post<?php echo $post->getId() ?>"></a>
      <?php echo $post->getContent() ?> 
    </div>
    <ul class="post_actions">
      <?php if ($sf_user->hasCredential('moderator')): ?>
        <li><?php echo link_to(__('Delete'), 'sfSimpleForum/deletePost?id='.$post->getId()) ?></li>
      <?php endif; ?>
    </ul>   
  </td>
</tr>
<tr class="spacer"><td colspan="2"></td></tr>