<?php

/**
 * Subclass for performing query and update operations on the 'sf_simple_forum_subforum' table.
 *
 * 
 *
 * @package plugins.sfSimpleForumPlugin.lib.model
 */ 
class PluginsfSimpleForumForumPeer extends BasesfSimpleForumForumPeer
{
  public static function retrieveByStrippedName($stripped_name)
  {
    $c = new Criteria();
    $c->add(self::STRIPPED_NAME, $stripped_name);

    return self::doSelectOne($c);
  }
  
  public static function getAllOrderedByCategory()
  {
    $c = new Criteria();
    $c->addJoin(self::CATEGORY_ID, sfSimpleForumCategoryPeer::ID);
    $c->addAscendingOrderByColumn(sfSimpleForumCategoryPeer::RANK);
    $c->addAscendingOrderByColumn(self::RANK);
    
    return self::doSelectJoinCategoryLeftJoinPost($c);
  }
  
  public static function getAllAsArray()
  {
    $forums = self::getAllOrderedByCategory();
    $res = array();
    
    foreach ($forums as $forum)
    {
      $res[$forum->getStrippedName()] = $forum->getName();
    }
    
    return $res;
  }
  
  public static function doSelectJoinCategoryLeftJoinPost(Criteria $c, $con = null)
  {
    
    foreach (sfMixer::getCallables('BasesfSimpleForumForumPeer:doSelectJoinAll:doSelectJoinAll') as $callable)
    {
      call_user_func($callable, 'BasesfSimpleForumForumPeer', $c, $con);
    }
    
    $c = clone $c;
    
    if ($c->getDbName() == Propel::getDefaultDB()) 
    {
      $c->setDbName(self::DATABASE_NAME);
    }
    
    sfSimpleForumForumPeer::addSelectColumns($c);
    $startcol2 = (sfSimpleForumForumPeer::NUM_COLUMNS - sfSimpleForumForumPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
    
    sfSimpleForumCategoryPeer::addSelectColumns($c);
    $startcol3 = $startcol2 + sfSimpleForumCategoryPeer::NUM_COLUMNS;
    
    sfSimpleForumPostPeer::addSelectColumns($c);
    $startcol4 = $startcol3 + sfSimpleForumPostPeer::NUM_COLUMNS;
    
    $c->addJoin(sfSimpleForumForumPeer::CATEGORY_ID, sfSimpleForumCategoryPeer::ID);
    
    $c->addJoin(sfSimpleForumForumPeer::LATEST_POST_ID, sfSimpleForumPostPeer::ID, Criteria::LEFT_JOIN);
    
    $rs = BasePeer::doSelect($c, $con);
    $results = array();
    
    while($rs->next())
    {
      
      $omClass = sfSimpleForumForumPeer::getOMClass();
      
      $cls = Propel::import($omClass);
      $obj1 = new $cls();
      $obj1->hydrate($rs);
      
      $omClass = sfSimpleForumCategoryPeer::getOMClass();
      
      $cls = Propel::import($omClass);
      $obj2 = new $cls();
      $obj2->hydrate($rs, $startcol2);
      
      $newObject = true;
      for ($j=0, $resCount=count($results); $j < $resCount; $j++)
      {
        $temp_obj1 = $results[$j];
        $temp_obj2 = $temp_obj1->getsfSimpleForumCategory();
        if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey())
        {
          $newObject = false;
          $temp_obj2->addsfSimpleForumForum($obj1);
          break;
        }
      }
      
      if ($newObject)
      {
        $obj2->initsfSimpleForumForums();
        $obj2->addsfSimpleForumForum($obj1);
      }
      
      $omClass = sfSimpleForumPostPeer::getOMClass();
      
      $cls = Propel::import($omClass);
      $obj3 = new $cls();
      $obj3->hydrate($rs, $startcol3);
      
      $newObject = true;
      for ($j=0, $resCount=count($results); $j < $resCount; $j++) 
      {
        $temp_obj1 = $results[$j];
        $temp_obj3 = $temp_obj1->getsfSimpleForumPost();
        if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey())
        {
          $newObject = false;
          $temp_obj3->addsfSimpleForumForum($obj1);
          break;
        }
      }
      
      if ($newObject)
      {
        $obj3->initsfSimpleForumForums();
        $obj3->addsfSimpleForumForum($obj1);
      }
      
      $results[] = $obj1;
    }
    return $results;
  }
}
