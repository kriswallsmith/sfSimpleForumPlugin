<?php

/**
 * Subclass for performing query and update operations on the 'sf_simple_forum_topic' table.
 *
 * 
 *
 * @package plugins.sfSimpleForumPlugin.lib.model
 */ 
class PluginsfSimpleForumTopicPeer extends BasesfSimpleForumTopicPeer
{
  public static function setIsNewForUser($topics, $user_id)
  {
    $topic_ids  = array();
    $topic_hash = array();
    foreach ($topics as $topic)
    {
      // A topic is new unless we can find a view from the user for it
      $topic->setIsNew(true);
      $id = $topic->getId();
      $topic_ids[] = $id;
      $topic_hash[$id] = $topic;
    }
    
    $c = new Criteria();
    $c->clearSelectColumns();
    $c->addSelectColumn(sfSimpleForumTopicViewPeer::TOPIC_ID);
    $c->add(sfSimpleForumTopicViewPeer::USER_ID, $user_id);
    $c->add(sfSimpleForumTopicViewPeer::TOPIC_ID, $topic_ids, Criteria::IN);
    $rs = sfSimpleForumTopicViewPeer::doSelectRS($c);
    
    while($rs->next())
    {
      $topic = $topic_hash[$rs->getInt(1)];
      $topic->setIsNew(false);
    }
    
    return $topic_hash;
  }
  
  public static function getLatestCriteria()
  {
    $c = new Criteria();
    $c->addDescendingOrderByColumn(self::UPDATED_AT);
    
    return $c;
  }  
  
  public static function getLatest($max = 10)
  {
    $c = self::getLatestCriteria();
    $c->setLimit($max);
    
    return self::doSelectJoinAll($c);
  }
  
  public static function getLatestPager($page = 1, $max_per_page = 10)
  {
    $c = self::getLatestCriteria();
    $pager = new sfPropelPager('sfSimpleForumTopic', $max_per_page);
    $pager->setPage($page);
    $pager->setCriteria($c);
    $pager->setPeerMethod('doSelectJoinAll');
    $pager->init();
    
    return $pager;
  }
  
  public static function getForUserCriteria($user_id)
  {
    $c = new Criteria();
    $c->add(self::USER_ID, $user_id);
    $c->addDescendingOrderByColumn(self::UPDATED_AT);
    
    return $c;
  }  
  
  public static function getForUser($user_id, $max = 10)
  {
    $c = self::getForUserCriteria($user_id);
    $c->setLimit($max);
    
    return self::doSelectJoinAll($c);
  }
  
  public static function getForUserPager($user_id, $page = 1, $max_per_page = 10)
  {
    $c = self::getForUserCriteria($user_id);
    $pager = new sfPropelPager('sfSimpleForumTopic', $max_per_page);
    $pager->setPage($page);
    $pager->setCriteria($c);
    $pager->setPeerMethod('doSelectJoinAll');
    $pager->init();
    
    return $pager;
  }
  
  public static function countForUser($user_id)
  {
    $c = new Criteria();
    $c->add(self::USER_ID, $user_id);
    
    return self::doCount($c);
  }
}
