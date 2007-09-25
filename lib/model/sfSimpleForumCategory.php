<?php

/**
 * Subclass for representing a row from the 'sf_simple_forum_category' table.
 *
 * 
 *
 * @package plugins.sfSimpleForumPlugin.lib.model
 */ 
class sfSimpleForumCategory extends BasesfSimpleForumCategory
{
  public function getFora()
  {
    $c = new Criteria();
    $c->add(sfSimpleForumForumPeer::CATEGORY_ID, $this->getId());
    return sfSimpleForumForumPeer::doSelect($c);
  }
  
  public function __toString()
  {
    return $this->getName();
  }
}
