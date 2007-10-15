<?php

/**
 * Subclass for representing a row from the 'sf_simple_forum_subforum' table.
 *
 * 
 *
 * @package plugins.sfSimpleForumPlugin.lib.model
 */ 
class PluginsfSimpleForumForum extends BasesfSimpleForumForum
{
  public function setName($name)
  {
    parent::setName($name);
    $this->setStrippedName(sfSimpleForumTools::stripText($name));
  }

  public function getTopics($max = 10)
  {
    $c = $this->getTopicsCriteria();
    $c->setLimit($max);
    
    return sfSimpleForumTopicPeer::doSelect($c);
  }

  public function getTopicsPager($page = 1, $max_per_page = 10)
  {
    $c = $this->getTopicsCriteria();
    $pager = new sfPropelPager('sfSimpleForumTopic', $max_per_page);
    $pager->setPage($page);
    $pager->setCriteria($c);
    $pager->setPeerMethod('doSelectJoinsfSimpleForumPost');
    $pager->init();

    return $pager;
  }
  
  public function getTopicsCriteria()
  {
    $c = new Criteria();
    $c->add(sfSimpleForumTopicPeer::FORUM_ID, $this->getId());
    $c->addDescendingOrderByColumn(sfSimpleForumTopicPeer::IS_STICKED);
    $c->addDescendingOrderByColumn(sfSimpleForumTopicPeer::UPDATED_AT);
    
    return $c;
  }

  public function getLatestPostByQuery()
  {
    $c = new Criteria();
    $c->add(sfSimpleForumPostPeer::FORUM_ID, $this->getId());
    $c->addDescendingOrderByColumn(sfSimpleForumPostPeer::CREATED_AT);
    
    return sfSimpleForumPostPeer::doSelectOne($c);
  }
  
  public function getLatestPost($con = null)
  {
    return $this->getsfSimpleForumPost($con);
  }
  
  public function getPosts($max)
  {
    return sfSimpleForumPostPeer::getForForum($this->getStrippedName(), $max);
  }

  public function getPostsPager($page = 1, $max_per_page = 10)
  {
    return sfSimpleForumPostPeer::getForForumPager($this->getStrippedName(), $page, $max_per_page);
  }
  
  public function updateCounts($latestReply = null, $con = null)
  {
    if($latestReply)
    {
      $this->setNbPosts($this->countsfSimpleForumPosts());
      $this->setNbTopics($this->countsfSimpleForumTopics());
      $this->setLatestPostId($latestReply->getId());
      $this->setUpdatedAt($latestReply->getCreatedAt());
    }
    else
    {
      $this->setNbPosts(0);
      $this->setNbTopics(0);
      $this->setLatestPostId(null);
    }
    $this->save($con);
  }
  
}
