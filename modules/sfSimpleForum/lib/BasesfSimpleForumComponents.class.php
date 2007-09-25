<?php

/**
 *
 * @package    symfony
 * @subpackage plugin             
 * @version    SVN: $Id$
 */

class BasesfSimpleForumComponents extends sfComponents
{
  public function executeLatestPosts()
  {
    $this->posts = sfSimpleForumPostPeer::getLatest(sfConfig::get('app_sfSimpleForumPlugin_max_per_block', 10));
  }
}
