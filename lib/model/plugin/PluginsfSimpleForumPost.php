<?php

/**
 * Subclass for representing a row from the 'sf_simple_forum_post' table.
 *
 * 
 *
 * @package plugins.sfSimpleForumPlugin.lib.model
 */ 
class PluginsfSimpleForumPost extends BasesfSimpleForumPost
{
  public function __toString()
  {
    return $this->getTitle();
  }

  public function getUser()
  {
    return sfSimpleForumTools::getUser($this);
  }
  
  public function setUserId($id)
  {
    parent::setUserId($id);
    $this->setAuthorName($this->getUser()->__toString());
  }
  
  public function getTopic()
  {
    return $this->getsfSimpleForumTopic();
  }
  
  public function setTopicId($id)
  {
    parent::setTopicId($id);
    if($this->isNew())
    {
      $topic = $this->getsfSimpleForumTopic();
      $this->setTitle($topic->getTitle());
      $this->setForumId($topic->getForumId());      
    }
  }
  
  public function getPositionInTopic()
  {
    $c = new Criteria();
    $c->clearSelectColumns();
    $c->addSelectColumn(sfSimpleForumPostPeer::ID);
    $c->add(sfSimpleForumPostPeer::TOPIC_ID, $this->getTopicId());
    $c->addAscendingOrderByColumn(sfSimpleForumPostPeer::CREATED_AT);
    $rs = sfSimpleForumPostPeer::doSelectRS($c);
    $messages = array();
    while($rs->next())
    {
      $messages[] = $rs->getInt(1);
    }

    return array_search($this->getId(), $messages);
  }
  
  public function save($con = null, $preserveTopic = false)
  {
    if(!$con)
    {
      $con = Propel::getConnection();
    }

    try
    {
      $con->begin();
      
      $topic = $this->getsfSimpleForumTopic();
      if($this->isNew())
      {
        $topic->clearViews();
      }
      
      parent::save($con);
      
      $latestPost = $topic->getLatestPostByQuery();
      if($preserveTopic)
      {
        $topic->leaveUpdatedAtUnchanged();
      }
      $topic->updateReplies($latestPost, $con);
      
      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollback();
      throw $e;
    }
  }

  public function delete($con = null, $preserveTopic = true)
  {
    if(!$con)
    {
      $con = Propel::getConnection();
    }

    try
    {
      $con->begin();
     
      parent::delete($con);
      
      $topic = $this->getsfSimpleForumTopic();
      if($preserveTopic)
      {
        $topic->leaveUpdatedAtUnchanged();
      }
      $latestPost = $topic->getLatestPostByQuery();
      $topic->updateReplies($latestPost, $con);
     
      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollback();
      throw $e;
    }
  }
  
  public function getFeedLink()
  {
    return 'sfSimpleForum/topic?id='.$this->getTopicId().'&stripped_title='.$this->getsfSimpleForumTopic()->getStrippedTitle();
  }
  
  public function getCreationTimestamp()
  {
    return $this->getCreatedAt('U');
  }
}
