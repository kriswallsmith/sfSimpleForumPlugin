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
    $this->setForumListVars();

    $forums = sfSimpleForumForumPeer::getAllOrderedByCategory();
    $threads = 0; 
    $posts = 0;

    foreach($forums as $forum)
    {
      $threads += $forum->getNbTopics();
      $posts += $forum->getNbPosts();
    }

    $this->forums = $forums;
    $this->threads = $threads;
    $this->posts = $posts;
    
    $this->breadcrumb = array(
      sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums')
    );
  }
  
  public function executeLatestPosts()
  {
    $this->setForumListVars();
    
    $this->post_pager = sfSimpleForumPostPeer::getLatestPager(
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    
    sfLoader::loadHelpers('I18N');
    $this->title = __('Latest messages');
    $this->breadcrumb = array(
      array(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'), 'sfSimpleForum/forumList'),
      $this->title
    );
    $this->rule = 'sfSimpleForum/latestPosts';
    $this->feed_rule = 'sfSimpleForum/latestPostsFeed';
    
    $this->nb_topics = sfSimpleForumTopicPeer::doCount(new Criteria());
    $this->topic_rule = 'sfSimpleForum/latestTopics';
    
    $this->setTemplate('postList');
  }
  
  public function executeLatestPostsFeed()
  {
    $this->setForumListVars();
        
    $this->checkFeedPlugin();
    
    $this->posts = sfSimpleForumPostPeer::getLatest(
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    
    $this->setForumListVars();
    $this->rule = 'sfSimpleForum/latestPosts';
    
    return $this->renderText($this->getFeedFromObjects($this->posts));
  }
  
  protected function setForumListVars()
  {
    sfLoader::loadHelpers('I18N');
    $this->feed_title =  __('Latest messages from %forums%', array(
      '%forums%'  => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
    ));
  }
  
  public function executeLatestTopics()
  {
    $this->setTopicListVars();
    
    $this->topics_pager = sfSimpleForumTopicPeer::getLatestPager(
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    
    $this->title = __('Latest topics');
    $this->breadcrumb = array(
      array(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'), 'sfSimpleForum/forumList'),
      $this->title
    );
    $this->rule = 'sfSimpleForum/latestTopics';
    $this->feed_rule = 'sfSimpleForum/latestTopicsFeed';
    
    $this->nb_posts = sfSimpleForumPostPeer::doCount(new Criteria());
    $this->post_rule = 'sfSimpleForum/latestPosts';
    
    $this->setTemplate('topicList');
  }
  
  public function executeLatestTopicsFeed()
  {
    $this->setTopicListVars();
        
    $this->checkFeedPlugin();
    
    $this->topics = sfSimpleForumTopicPeer::getLatest(
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    
    $this->rule = 'sfSimpleForum/latestTopics';
    
    return $this->renderText($this->getFeedFromObjects($this->topics));
  }
  
  protected function setTopicListVars()
  {
    sfLoader::loadHelpers('I18N');
    $this->feed_title =  __('Latest topics from %forums%', array(
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
    
    $this->breadcrumb = array(
      array(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'), 'sfSimpleForum/forumList'),
      $this->forum->getName()
    );
  }

  public function executeLatestForumPosts()
  {
    $this->setForumVars();

    $this->post_pager = $this->forum->getPostsPager(
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    
    sfLoader::loadHelpers('I18N');
    $this->title = __('Latest messages');
    $this->rule = 'sfSimpleForum/latestForumPosts?forum_name='.$this->name;
    $this->feed_rule = 'sfSimpleForum/latestForumPostsFeed?forum_name='.$this->name;

    $this->nb_topics = $this->forum->countsfSimpleForumTopics();
    $this->topic_rule = 'sfSimpleForum/forum?forum_name='.$this->name;

    $this->breadcrumb = array(
      array(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'), 'sfSimpleForum/forumList'),
      array($this->forum->getName(), 'sfSimpleForum/forum?forum_name='.$this->name),
      $this->title
    );       
    
    $this->setTemplate('postList');
  }
    
  public function executeLatestForumPostsFeed()
  {
    $this->setForumVars();
    
    $this->checkFeedPlugin();
    
    $this->posts = $this->forum->getPosts(
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    
    $this->rule = 'sfSimpleForum/latestForumPosts?forum_name='.$this->name;
    
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
    $this->topic = sfSimpleForumTopicPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($this->topic);
    $this->post_pager = $this->topic->getPostsPager(
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    $this->forward404Unless($this->post_pager);

    $this->posts = $this->post_pager->getResults();
    
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
    
    $this->setTopicVars();
  }
  
  public function executeTopicFeed()
  {
    $this->checkFeedPlugin();
    
    $this->topic = sfSimpleForumTopicPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($this->topic);
    $this->posts = $this->topic->getPosts(
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    $this->forward404Unless($this->posts);
    
    $this->setTopicVars();
    
    $this->rule = 'sfSimpleForum/topic?id='.$this->getRequestParameter('id').'&stripped_title='.$this->getRequestParameter('forum_name');
    
    return $this->renderText($this->getFeedFromObjects($this->posts));
  }
  
  protected function setTopicVars()
  {
    sfLoader::loadHelpers('I18N');
    $forum_name = sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums');
    
    $this->breadcrumb = array(
      array($forum_name, 'sfSimpleForum/forumList'),
      array($this->topic->getsfSimpleForumForum()->getName(), 'sfSimpleForum/forum?forum_name='.$this->topic->getsfSimpleForumForum()->getStrippedName()),
      $this->topic->getTitle()
    );
    
    $this->feed_title = __('Latest messages from %forums% » %forum% » %topic%', array(
      '%forums%'  => $forum_name,
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

  public function executeLatestUserPosts()
  {
    $this->setUserVars();
        
    $this->post_pager = sfSimpleForumPostPeer::getForUserPager(
      $this->user->getId(),
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    
    sfLoader::loadHelpers('I18N');
    $this->title = __('Messages by %user%', array('%user%' => $this->username));
    $this->breadcrumb = array(
      array(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'), 'sfSimpleForum/forumList'),
      $this->title
    );
    
    $this->rule = 'sfSimpleForum/latestUserPosts?username='.$this->username;
    $this->feed_rule = 'sfSimpleForum/latestUserPostsFeed?username='.$this->username;
    $this->feed_title = __('Latest messages from %forums% by %username%', array(
      '%forums%'   => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
      '%username%' => $this->user->getUsername(),
    ));
    
    $this->nb_topics = sfSimpleForumTopicPeer::countForUser($this->user->getId());
    $this->topic_rule = 'sfSimpleForum/latestUserTopics?username='.$this->username;
    
    $this->setTemplate('postList');
  }
  
  public function executeLatestUserPostsFeed()
  {
    $this->setUserVars();
    
    $this->posts = sfSimpleForumPostPeer::getForUser(
      $this->user->getId(),
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    
    $this->rule = 'sfSimpleForum/latestUserPosts?username='.$this->username;
    $this->feed_title = __('Latest messages from %forums% by %username%', array(
      '%forums%'   => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
      '%username%' => $this->user->getUsername(),
    ));
    
    return $this->renderText($this->getFeedFromObjects($this->posts));
  }
  
  public function executeLatestUserTopics()
  {
    $this->setUserVars();
        
    $this->topics_pager = sfSimpleForumTopicPeer::getForUserPager(
      $this->user->getId(),
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    $this->nb_topics = $this->topics_pager->getNbResults();
    
    sfLoader::loadHelpers('I18N');
    $this->title = __('Topics by %user%', array('%user%' => $this->username));
    $this->breadcrumb = array(
      array(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'), 'sfSimpleForum/forumList'),
      $this->title
    );
    $this->rule = 'sfSimpleForum/latestUserTopics?username='.$this->username;
    $this->feed_rule = 'sfSimpleForum/latestUserTopicsFeed?username='.$this->username;
    $this->feed_title = __('Latest topics from %forums% by %username%', array(
      '%forums%'   => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
      '%username%' => $this->user->getUsername(),
    ));
    
    $this->nb_posts = sfSimpleForumPostPeer::countForUser($this->user->getId());
    $this->post_rule = 'sfSimpleForum/latestUserPosts?username='.$this->username;
    
    $this->setTemplate('topicList');
  }
  
  public function executeLatestUserTopicsFeed()
  {
    $this->setUserVars();
    
    $this->topics = sfSimpleForumTopicPeer::getForUser(
      $this->user->getId(),
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    
    $this->rule = 'sfSimpleForum/latestUserTopics?username='.$this->username;
    $this->feed_title = __('Latest topics from %forums% by %username%', array(
      '%forums%'   => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
      '%username%' => $this->user->getUsername(),
    ));
    
    return $this->renderText($this->getFeedFromObjects($this->topics));
  }
  protected function setUserVars()
  {
    $this->username = $this->getRequestParameter('username');
    
    $this->user = sfGuardUserPeer::retrieveByUsername($this->username);
    $this->forward404Unless($this->user);
    
    sfLoader::loadHelpers('I18N');
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
    
    $this->topic_name = '';
    $this->topic_id = null;
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
