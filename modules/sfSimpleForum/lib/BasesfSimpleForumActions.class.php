<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2007 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2007 Nick Winfield <enquiries@superhaggis.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Nick Winfield <enquiries@superhaggis.com>              
 * @version    SVN: $Id$
 */

class BasesfSimpleForumActions extends sfActions
{
  public function executeIndex()
  {
    $this->forward('sfSimpleForum', 'forumList');
  }
  
  public function executeForumList()
  {
    $forums = sfSimpleForumForumPeer::getAllOrderedByCategory();
    $nb_topics = 0; 
    $nb_posts  = 0;

    foreach($forums as $forum)
    {
      $nb_topics += $forum->getNbTopics();
      $nb_posts  += $forum->getNbPosts();
    }

    $this->forums = $forums;
    $this->nb_topics = $nb_topics;
    $this->nb_posts  = $nb_posts;
    $this->feed_title = $this->getFeedTitle();
  }
  
  public function executeLatestPosts()
  {
    $this->post_pager = sfSimpleForumPostPeer::getLatestPager(
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    $this->nb_topics = sfSimpleForumTopicPeer::doCount(new Criteria());
    $this->feed_title = $this->getFeedTitle();
  }
  
  public function executeLatestPostsFeed()
  {
    $this->checkFeedPlugin();
    
    $this->posts = sfSimpleForumPostPeer::getLatest(
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    $this->rule = 'sfSimpleForum/latestPosts';
    $this->feed_title = $this->getFeedTitle();
    
    return $this->renderText($this->getFeedFromObjects($this->posts));
  }
  
  protected function getFeedTitle()
  {
    sfLoader::loadHelpers('I18N');
    return __('Latest messages from %forums%', array(
      '%forums%'  => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
    ));
  }
  
  public function executeLatestTopics()
  {
    $this->topics_pager = sfSimpleForumTopicPeer::getLatestPager(
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    $this->feed_title = $this->getLatestTopicsFeedTitle();
  }
  
  public function executeLatestTopicsFeed()
  {
    $this->checkFeedPlugin();
    
    $this->topics = sfSimpleForumTopicPeer::getLatest(
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    $this->rule = 'sfSimpleForum/latestTopics';
    $this->feed_title = $this->getLatestTopicsFeedTitle();
    
    return $this->renderText($this->getFeedFromObjects($this->topics));
  }
  
  protected function getLatestTopicsFeedTitle()
  {
    sfLoader::loadHelpers('I18N');
    return __('Latest topics from %forums%', array(
      '%forums%'  => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
    ));
  }
  
  // One forum
  
  public function executeForum()
  {
    $this->setForumVars();
        
    $this->topic_pager = $this->forum->getTopicsPager(
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    $this->topics = $this->topic_pager->getResults();
    
    if (sfConfig::get('app_sfSimpleForumPlugin_count_views', true) && $this->getUser()->isAuthenticated())
    {
      // FIXME: When Propel can do a right join with multiple on conditions, merge this query with the pager's one
      $this->topics = sfSimpleForumPostPeer::setIsNewForUser($this->topics, $this->getUser()->getGuardUser()->getId());
    }
  }

  public function executeForumLatestPosts()
  {
    $this->setForumVars();

    $this->post_pager = $this->forum->getPostsPager(
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
  }
    
  public function executeForumLatestPostsFeed()
  {
    $this->checkFeedPlugin();
    
    $this->setForumVars();    
    $this->posts = $this->forum->getPosts(
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    $this->rule = 'sfSimpleForum/forumLatestPosts?forum_name='.$this->name;
    
    return $this->renderText($this->getFeedFromObjects($this->posts));
  }
    
  protected function setForumVars()
  {
    $this->name = $this->getRequestParameter('forum_name');

    $forum = sfSimpleForumForumPeer::retrieveByStrippedName($this->name);
    $this->forward404Unless($forum);
    $this->forum = $forum;

    sfLoader::loadHelpers('I18N');
    $this->feed_title =  __('Latest messages from %forums% » %forum%', array(
      '%forums%'  => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
      '%forum%'   => $this->forum->getName()
    ));
  }
  
  // One topic

  public function executeTopic()
  {
    $this->setTopicVars();
    $this->post_pager = $this->topic->getPostsPager(
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    $this->forward404Unless($this->post_pager);
    
    if (sfConfig::get('app_sfSimpleForumPlugin_count_views', true))
    {
      // lame protection against simple page refreshing
      if($this->getUser()->getAttribute('sf_simple_forum_latest_viewed_topic') != $this->topic->getId())
      {
        $this->topic->incrementViews();
        $this->getUser()->setAttribute('sf_simple_forum_latest_viewed_topic', $this->topic->getId());
      }
      if($this->getUser()->isAuthenticated())
      {
        $this->topic->addViewForUser($this->getUser()->getGuardUser()->getId());
      }
    }
  }
  
  public function executeTopicFeed()
  {
    $this->checkFeedPlugin();
    $this->setTopicVars();
    $this->posts = $this->topic->getPosts(
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    $this->forward404Unless($this->posts);
    
    $this->rule = 'sfSimpleForum/topic?id='.$this->getRequestParameter('id').'&stripped_title='.$this->getRequestParameter('forum_name');
    
    return $this->renderText($this->getFeedFromObjects($this->posts));
  }
  
  protected function setTopicVars()
  {
    $this->topic = sfSimpleForumTopicPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($this->topic);

    sfLoader::loadHelpers('I18N');
    $this->feed_title =  __('Latest messages from %forums% » %forum% » %topic%', array(
      '%forums%'  => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
      '%forum%'   => $this->topic->getsfSimpleForumForum()->getName(),
      '%topic%'   => $this->topic->getTitle()
    ));
  }
  
  public function executePost()
  {
    $post = sfSimpleForumPostPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($post);

    $topic = $post->getTopic();
    $position = $post->getPositionInTopic();
    $page = ceil(($position + 1) / sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10));
      $this->redirect('sfSimpleForum/topic?id='.$topic->getId().'&stripped_title='.$topic->getStrippedTitle().'&page='.$page.'#post'.$post->getId());
  }
  
  // One user

  public function executeUserLatestPosts()
  {
    $this->setUserVars();
        
    $this->post_pager = sfSimpleForumPostPeer::getForUserPager(
      $this->user->getId(),
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    $this->feed_title = $this->getUserLatestPostsFeedTitle();
  }
  
  public function executeUserLatestPostsFeed()
  {
    $this->setUserVars();
    
    $this->posts = sfSimpleForumPostPeer::getForUser(
      $this->user->getId(),
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    
    $this->rule = 'sfSimpleForum/userLatestPosts?username='.$this->username;
    $this->feed_title = $this->getUserLatestPostsFeedTitle();
    
    return $this->renderText($this->getFeedFromObjects($this->posts));
  }
  
  protected function getUserLatestPostsFeedTitle()
  {
    sfLoader::loadHelpers('I18N');
    return __('Latest messages from %forums% by %username%', array(
      '%forums%'   => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
      '%username%' => $this->user->getUsername(),
    ));
  }
  
  public function executeUserLatestTopics()
  {
    $this->setUserVars();
        
    $this->topics_pager = sfSimpleForumTopicPeer::getForUserPager(
      $this->user->getId(),
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    
    $this->feed_title = $this->getUserLatestTopicsFeedTitle();
  }
  
  public function executeUserLatestTopicsFeed()
  {
    $this->setUserVars();
    
    $this->topics = sfSimpleForumTopicPeer::getForUser(
      $this->user->getId(),
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    $this->rule = 'sfSimpleForum/latestUserTopics?username='.$this->username;
    $this->feed_title = $this->getUserLatestTopicsFeedTitle();
    
    return $this->renderText($this->getFeedFromObjects($this->topics));
  }
  
  protected function getUserLatestTopicsFeedTitle()
  {
    sfLoader::loadHelpers('I18N');
    return __('Latest topics from %forums% by %username%', array(
      '%forums%'   => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
      '%username%' => $this->user->getUsername(),
    ));
  }
  
  protected function setUserVars()
  {
    $this->username = $this->getRequestParameter('username');
    $this->user = sfGuardUserPeer::retrieveByUsername($this->username);
    $this->forward404Unless($this->user);
  }
  
  // Feed related private methods
  
  protected function checkFeedPlugin()
  {
    if(!class_exists('sfFeedPeer'))
    {
      throw new sfException('You must install sfFeed2Plugin to use the feed actions');
    }
  }
  
  protected function getFeedFromObjects($objects)
  {
    $feed = sfFeedPeer::createFromObjects(
      $objects,
      array(
        'format'      => 'atom1',
        'title'       => $this->feed_title,
        'link'        => $this->rule,
        'methods'     => array('authorEmail' => '')
      )
    );
    $this->setLayout(false);
    return $feed->asXml();
  }
  
  // Display the topic creation form
  
  public function executeCreateTopic()
  {
    $this->forum = sfSimpleForumForumPeer::retrieveByStrippedName($this->getRequestParameter('forum_name'));
    if(!sfConfig::get('app_sfSimpleForumPlugin_allow_new_topic_outside_forum', true))
    {
      $this->forward404Unless($this->forum);
    }
  }
  
  // Handle the topic creation
  
  public function handleErrorAddTopic()
  {
    $this->getRequest()->setAttribute('topic', sfSimpleForumPostPeer::retrieveByPk($this->getRequestParameter('topic_id')));
    $this->forward('sfSimpleForum', 'createTopic');
  }
  
  public function executeAddTopic()
  {    
    $forum = sfSimpleForumForumPeer::retrieveByStrippedName($this->getRequestParameter('forum_name'));
    $this->forward404Unless($forum);

    $topic = new sfSimpleForumTopic();
    $topic->setsfSimpleForumForum($forum);
    $topic->setTitle($this->getRequestParameter('title'));
    $topic->setUserId($this->getUser()->getGuardUser()->getId());
    if ($this->getUser()->hasCredential('moderator'))
    {
      $topic->setIsSticked($this->getRequestParameter('is_sticked', 0));
      $topic->setIsLocked($this->getRequestParameter('is_locked', 0));
    }
    $topic->save();
        
    $post = new sfSimpleForumPost();
    $post->setContent($this->getRequestParameter('body'));
    $post->setUserId($this->getUser()->getGuardUser()->getId());
    $post->setsfSimpleForumTopic($topic);
    $post->save();
    
    $this->redirectToPost($post);
  }
  
  // Handle the post creation
  
  public function handleErrorAddPost()
  {
    $this->getRequest()->setParameter('id', $this->getRequestParameter('topic_id'));
    $this->forward('sfSimpleForum', 'topic');
  }
  
  public function executeAddPost()
  {    
    $topic = sfSimpleForumTopicPeer::retrieveByPK($this->getRequestParameter('topic_id'));
    $this->forward404Unless($topic);
    // We must check if the topic isn't locked
    $this->forward404If($topic->getIsLocked());
    
    $post = new sfSimpleForumPost();
    $post->setContent($this->getRequestParameter('body'));
    $post->setUserId($this->getUser()->getGuardUser()->getId());
    $post->setTopicId($topic->getId());
    $post->save();
    
    $this->redirectToPost($post);
  }
  
  protected function redirectToPost($post)
  {
    $position = $post->getPositionInTopic();
    $page = ceil(($position + 1) / sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10));
    $this->redirect('sfSimpleForum/topic?id='.$post->getTopic()->getId().'&stripped_title='.$post->getTopic()->getStrippedTitle().'&page='.$page.'#post'.$post->getId());    
  }
  
  public function executeDeletePost()
  {
    $post = sfSimpleForumPostPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($post);
    
    $topic = $post->getTopic();
    if ($topic->countsfSimpleForumPosts() == 1) 
    {
      // it is the last post of the topic, so delete the whole topic
      $topic->delete();
      $forum = $post->getsfSimpleForumForum();
      $this->redirect('sfSimpleForum/forum?forum_name='.$forum->getStrippedName());
    }
    else
    {
      // delete only one message and redirect to the topic
      $post->delete();
      $this->redirect('sfSimpleForum/topic?id='.$topic->getId().'&stripped_title='.$topic->getStrippedTitle());
    }
  }
  
  public function executeDeleteTopic()
  {
    $topic = sfSimpleForumTopicPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($topic);
    
    $topic->delete();
    
    $forum = $topic->getsfSimpleForumForum();
    $this->redirect('sfSimpleForum/forum?forum_name='.$forum->getStrippedName());
  }
  
  // stick/unstick a topic
  
  public function executeToggleStick()
  {
    $topic = sfSimpleForumTopicPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($topic);
    
    $topic->setIsSticked(!$topic->getIsSticked());
    $topic->leaveUpdatedAtUnchanged();
    $topic->save();
    
    $this->redirect('sfSimpleForum/topic?id='.$topic->getId());
  }
  
  // lock/unlock a topic
  
  public function executeToggleLock()
  {
    $topic = sfSimpleForumTopicPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($topic);
    
    $topic->setIsLocked(!$topic->getIsLocked());
    $topic->leaveUpdatedAtUnchanged();
    $topic->save();
    
    $this->redirect('sfSimpleForum/topic?id='.$topic->getId());
  }
}
