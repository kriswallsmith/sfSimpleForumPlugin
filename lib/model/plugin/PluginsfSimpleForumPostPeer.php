<?php

/**
 * Subclass for performing query and update operations on the 'sf_simple_forum_post' table.
 *
 * 
 *
 * @package plugins.sfSimpleForumPlugin.lib.model
 */ 
class PluginsfSimpleForumPostPeer extends BasesfSimpleForumPostPeer
{
  
  public static function getOneJoinForum($id)
  {
    $c = new Criteria();
    $c->add(self::ID, $id);
    $c->setLimit(1);
    
    $objects = self::doSelectJoinsfSimpleForumForum($c);
    
    if ($objects) 
    {
      return $objects[0];
    }
    return null;
  }
  public static function getLatestCriteria()
  {
    $c = new Criteria();
    $c->addDescendingOrderByColumn(self::ID);
    
    return $c;
  }  
  
  public static function getLatest($max = null)
  {
    $c = self::getLatestCriteria();
    if($max)
    {
      $c->setLimit($max);
    }
    
    return self::doSelect($c);
  }
  
  public static function getLatestPager($page = 1, $max_per_page = 10)
  {
    $c = self::getLatestCriteria();
    $pager = new sfPropelPager('sfSimpleForumPost', $max_per_page);
    $pager->setPage($page);
    $pager->setCriteria($c);
    $pager->setPeerMethod('doSelectJoinsfSimpleForumForum');
    $pager->init();
    
    return $pager;
  }
  
  public static function getForTopicCriteria($topic_id)
  {
    $c = new Criteria();
    $c->add(sfSimpleForumPostPeer::TOPIC_ID, $topic_id);
    
    return $c;
  }
  
  public static function getForTopic($topic_id, $max = null)
  {
    $c = self::getForTopicCriteria($topic_id);
    $c->addDescendingOrderByColumn(self::ID);
    if($max)
    {
      $c->setLimit($max);
    }
    $posts = self::doSelect($c);
    
    return $posts;
  }
  
  public static function getForTopicPager($topic_id, $page = 1, $max_per_page = 10)
  {
    $c = self::getForTopicCriteria($topic_id);
    $c->addAscendingOrderByColumn(self::ID);
    $pager = new sfPropelPager('sfSimpleForumPost', $max_per_page);
    $pager->setPage($page);
    $pager->setCriteria($c);
    $pager->init();
    
    return $pager;
  }

  public static function getForForumCriteria($forum_name)
  {
    $c = self::getLatestCriteria();
    $c->addJoin(self::FORUM_ID, sfSimpleForumForumPeer::ID);
    $c->add(sfSimpleForumForumPeer::STRIPPED_NAME, $forum_name);

    return $c;
  }  
  
  public static function getForForum($forum_name, $max = null)
  {
    $c = self::getForForumCriteria($forum_name);
    if($max)
    {
      $c->setLimit($max);
    }
    
    return self::doSelect($c);
  }
  
  public static function getForForumPager($forum_name = '', $page = 1, $max_per_page = 10)
  {
    $c = self::getForForumCriteria($forum_name);
    $pager = new sfPropelPager('sfSimpleForumPost', $max_per_page);
    $pager->setPage($page);
    $pager->setCriteria($c);
    $pager->setPeerMethod('doSelectJoinsfSimpleForumForum');
    $pager->init();

    return $pager;
  }
    
  public static function getForUserCriteria($user_id)
  {
    $c = new Criteria();
    $c->add(self::USER_ID, $user_id);
    $c->addDescendingOrderByColumn(self::ID);

    return $c;
  }  

  public static function getForUser($user_id, $max = null)
  {
    $c = self::getForUserCriteria($user_id);
    if($max)
    {
      $c->setLimit($max);
    }
    
    return self::doSelect($c);
  }

  public static function getForUserPager($user_id, $page = 1, $max_per_page = 10)
  {
    $c = self::getForUserCriteria($user_id);
    $pager = new sfPropelPager('sfSimpleForumPost', $max_per_page);
    $pager->setPage($page);
    $pager->setCriteria($c);
    $pager->setPeerMethod('doSelectJoinsfSimpleForumForum');
    $pager->init();

    return $pager;
  }
  
  public static function countForUser($user_id)
  {
    $c = new Criteria();
    $c->add(self::USER_ID, $user_id);
    
    return self::doCount($c);
  }
  
  /**
   * Selects a collection of sfSimpleForumPost objects pre-filled with related topic and forum objects.
   *
   * @return     array Array of sfSimpleForumPost objects.
   * @throws     PropelException Any exceptions caught during processing will be
   *             rethrown wrapped into a PropelException.
   */
  public static function doSelectJoinTopicAndForum(Criteria $c, $con = null)
  {
    foreach (sfMixer::getCallables('BasesfSimpleForumPostPeer:doSelectJoinAllExcept:doSelectJoinAllExcept') as $callable)
    {
      call_user_func($callable, 'BasesfSimpleForumPostPeer', $c, $con);
    }
    
    $c = clone $c;
    
    if ($c->getDbName() == Propel::getDefaultDB())
    {
      $c->setDbName(self::DATABASE_NAME);
    }
    
    sfSimpleForumPostPeer::addSelectColumns($c);
    $startcol2 = (sfSimpleForumPostPeer::NUM_COLUMNS - sfSimpleForumPostPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
    sfSimpleForumTopicPeer::addSelectColumns($c);
    $startcol3 = $startcol2 + sfSimpleForumTopicPeer::NUM_COLUMNS;
    sfSimpleForumForumPeer::addSelectColumns($c);
    $startcol4 = $startcol3 + sfSimpleForumForumPeer::NUM_COLUMNS;
    $c->addJoin(sfSimpleForumPostPeer::TOPIC_ID, sfSimpleForumTopicPeer::ID);
    $c->addJoin(sfSimpleForumPostPeer::FORUM_ID, sfSimpleForumForumPeer::ID);
    
    $rs = BasePeer::doSelect($c, $con);
    $results = array();
    
    while($rs->next()) 
    {
      $post = new sfSimpleForumPost();
      $post->hydrate($rs);
      
      $topic = new sfSimpleForumTopic();
      $topic->hydrate($rs, $startcol2);
      
      $newObject = true;
      foreach ($results as $temp_post)
      {
        $existing_topic = $temp_post->getsfSimpleForumTopic();
        if ($existing_topic->getPrimaryKey() === $topic->getPrimaryKey())
        {
          $newObject = false;
          $existing_topic->addsfSimpleForumPost($post);
          break;
        }
      }
      
      if ($newObject)
      {
        $topic->initsfSimpleForumPosts();
        $topic->addsfSimpleForumPost($post);
      }
      
      $forum = new sfSimpleForumForum();
      $forum->hydrate($rs, $startcol3);
      
      $newObject = true;
      foreach ($results as $temp_post)
      {
        $existing_forum = $temp_post->getsfSimpleForumForum();
        if ($existing_forum->getPrimaryKey() === $forum->getPrimaryKey())
        {
          $newObject = false;
          $existing_forum->addsfSimpleForumPost($post);
          break;
        }
      }
      
      if ($newObject)
      {
        $forum->initsfSimpleForumPosts();
        $forum->addsfSimpleForumPost($post);
      }
      
      $results[] = $post;
    }
    return $results;
  }
  
}
