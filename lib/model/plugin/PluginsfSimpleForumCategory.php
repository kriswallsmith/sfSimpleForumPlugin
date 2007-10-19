<?php

/**
 * Subclass for representing a row from the 'sf_simple_forum_category' table.
 *
 * 
 *
 * @package plugins.sfSimpleForumPlugin.lib.model
 */ 
class PluginsfSimpleForumCategory extends BasesfSimpleForumCategory
{
  public function getFora()
  {
    $c = new Criteria();
    $c->add(sfSimpleForumForumPeer::CATEGORY_ID, $this->getId());
    $c->addAscendingOrderByColumn(sfSimpleForumForumPeer::RANK);
    return sfSimpleForumForumPeer::doSelect($c);
  }
  
  public function __toString()
  {
    return $this->getName();
  }
}
