<?php

/**
 * Subclass for representing a row from the 'sf_simple_forum_topic' table.
 *
 * 
 *
 * @package plugins.sfSimpleForumPlugin.lib.model
 */ 
class PluginsfSimpleForumTopic extends BasesfSimpleForumTopic
{
  protected
    $is_new   = false;
  
  public function getIsNew()
  {
    return $this->is_new;
  }
  
  public function setIsNew($value = true)
  {
    $this->is_new = $value;
  }
  
  public function getUser()
  {
    return sfSimpleForumTools::getUser($this);
  }
  
  public function setTitle($title)
  {
    parent::setTitle($title);
    $this->setStrippedTitle(sfSimpleForumTools::stripText($title));
  }
  
  public function leaveUpdatedAtUnchanged()
  {
    $this->modifiedColumns[] = sfSimpleForumTopicPeer::UPDATED_AT;
  }
  
  public function incrementViews()
  {
    $this->setNbViews($this->getNbViews() + 1);
    // Preserve the previous update date to avoid changing the topic order
    $this->leaveUpdatedAtUnchanged();
    parent::save();
  }
  
  public function addViewForUser($user_id)
  {
    //check if there is not already a topic view for this user
    if (sfSimpleForumTopicViewPeer::retrieveByPk($user_id, $this->getId()))
    {
      return;
    }
    $topicView = new sfSimpleForumTopicView();
    $topicView->setTopicId($this->getId());
    $topicView->setUserId($user_id);
    $topicView->save();
  }
  
  public function getViewForUser($user_id)
  {
    return sfSimpleForumTopicViewPeer::retrieveByPk($user_id, $this->getId());
  }
  
  public function clearViews()
  {
    $c = new Criteria();
    $c->add(sfSimpleForumTopicViewPeer::TOPIC_ID, $this->getId());
    sfSimpleForumTopicViewPeer::doDelete($c);
  }
  
  public function getNbReplies()
  {
    return $this->getNbPosts() - 1;
  }
  
  public function getLatestPostByQuery()
  {
    $c = new Criteria();
    $c->add(sfSimpleForumPostPeer::TOPIC_ID, $this->getId());
    $c->addDescendingOrderByColumn(sfSimpleForumPostPeer::CREATED_AT);
    
    return sfSimpleForumPostPeer::doSelectOne($c);
  }
  
  public function getLatestPost()
  {
    return $this->getsfSimpleForumPost();
  }
  
  public function getPosts($max = null)
  {
    return sfSimpleForumPostPeer::getForTopic($this->getId(), $max);
  }

  public function getPostsPager($page = 1, $max_per_page = 10)
  {
    return sfSimpleForumPostPeer::getForTopicPager($this->getId(), $page, $max_per_page);
  }
  
  public function updateReplies($latestReply = null, $con = null)
  {
    if(!$this->isDeleted())
    {
      if($latestReply)
      {
        $this->setNbPosts($this->countsfSimpleForumPosts());
        $this->setLatestPostId($latestReply->getId());
        $this->setUpdatedAt($latestReply->getCreatedAt());
      }
      else
      {
        $this->setNbPosts(0);
        $this->setLatestPostId(null);
      }
      $this->save($con, $latestReply);
    }
  }

  public function save($con = null, $latestPost = null)
  {
    if(!$con)
    {
      $con = Propel::getConnection();
    }

    try
    {
      $con->begin();
      
      parent::save($con);
      
      // Update the topic's forum counts
      $forum = $this->getsfSimpleForumForum();
      if(!$latestPost)
      {
        $latestPost = $forum->getLatestPostByQuery();
      }
      $forum->updateCounts($latestPost, $con);
     
      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollback();
      throw $e;
    }
  }
    
  public function delete($con = null, $latestPost = null)
  {
    if(!$con)
    {
      $con = Propel::getConnection();
    }

    try
    {
      $con->begin();
      
      parent::delete($con);
      
      // Update the topic's forum counts
      $forum = $this->getsfSimpleForumForum();
      if(!$latestPost)
      {
        $latestPost = $forum->getLatestPostByQuery();
      }
      $forum->updateCounts($latestPost, $con);
      
      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollback();
      throw $e;
    }
  }
}
