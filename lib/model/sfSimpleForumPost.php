<?php

/**
 * Subclass for representing a row from the 'sf_simple_forum_post' table.
 *
 * 
 *
 * @package plugins.sfSimpleForumPlugin.lib.model
 */ 
class sfSimpleForumPost extends BasesfSimpleForumPost
{
  public function getUser()
  {
    return $this->getsfGuardUser();
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
  
  public function save($con = null)
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
      $topic->updateReplies($latestPost, $con);
      $this->getsfSimpleForumForum()->updateCounts($latestPost, $con);
      
      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollback();
      throw $e;
    }
  }

  public function delete($con = null)
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
      $latestPost = $topic->getLatestPostByQuery();
      $topic->setUpdatedAt($latestPost->getCreatedAt());
      $topic->updateReplies($latestPost, $con);
      
      $forum = $this->getsfSimpleForumForum();
      $latestPost = $forum->getLatestPostByQuery();
      $forum->setUpdatedAt($latestPost->getCreatedAt());
      $forum->updateCounts($latestPost, $con);
     
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
  
}
