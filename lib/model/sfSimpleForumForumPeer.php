<?php

/**
 * Subclass for performing query and update operations on the 'sf_simple_forum_subforum' table.
 *
 * 
 *
 * @package plugins.sfSimpleForumPlugin.lib.model
 */ 
class sfSimpleForumForumPeer extends BasesfSimpleForumForumPeer
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

    return self::doSelectJoinAll($c);
  }
}
