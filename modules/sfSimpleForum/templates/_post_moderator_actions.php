<?php echo use_helper('I18N') ?>
<ul class="post_actions">
  <li><?php echo link_to(__('Delete'), 'sfSimpleForum/deletePost?id='.$post->getId()) ?></li>
</ul>   
