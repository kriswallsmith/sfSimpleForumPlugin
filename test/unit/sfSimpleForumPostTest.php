<?php
/*
 * This file is part of the sfSimpleForum package.
 * 
 * (c) 2007 Francois Zaninotto <francois.zaninotto@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Unit tests for the sfSimpleForumPost Model.
 *
 * Despite running unit tests, we use the functional tests bootstrap to take advantage of propel
 * classes autoloading...
 * 
 * In order to run the tests in your context, you have to copy this file in a symfony test directory
 * and configure it appropriately (see the "configuration" section at the beginning of the file)
 *  
 * @author   Francois Zaninotto <francois.zaninotto@symfony-project.com>
 */

// configuration
// Autofind the first available app environment
$sf_root_dir = realpath(dirname(__FILE__).'/../../../../');
$apps_dir = glob($sf_root_dir.'/apps/*', GLOB_ONLYDIR);
$app = substr($apps_dir[0], 
              strrpos($apps_dir[0], DIRECTORY_SEPARATOR) + 1, 
              strlen($apps_dir[0]));
if (!$app)
{
  throw new Exception('No app has been detected in this project');
}

// -- path to the symfony project where the plugin resides
$sf_path = dirname(__FILE__).'/../../../..';
 
// bootstrap
include($sf_path . '/test/bootstrap/functional.php');

// create a new test browser
$browser = new sfTestBrowser();
$browser->initialize();

// initialize database manager
$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();

$con = Propel::getConnection();

// cleanup database
sfSimpleForumCategoryPeer::doDeleteAll();
sfSimpleForumForumPeer::doDeleteAll();
sfSimpleForumPostPeer::doDeleteAll();

// Now we can start to test
$t = new lime_test(34, new lime_output_color());

// Initialization
$cat = new sfSimpleForumCategory();
$cat->save();
$forum = new sfSimpleForumForum();
$forum->setCategoryId($cat->getId());
$forum->save();
$topic = new sfSimpleForumTopic();
$topic->setTitle('This is a test');
$topic->setsfSimpleForumForum($forum);
$topic->save();
$user0 = new sfGuardUser();
$user0->setUsername('me');
$user0->setPassword('me');
$user0->save();
$user1 = new sfGuardUser();
$user1->setUsername('foo');
$user1->setPassword('foo');
$user1->save();
$user2 = new sfGuardUser();
$user2->setUsername('bar');
$user2->setPassword('bar');
$user2->save();
$user3 = new sfGuardUser();
$user3->setUsername('baz');
$user3->setPassword('baz');
$user3->save();

$t->diag('Adding new messages');
sleep(1);
$msg1 = new sfSimpleForumPost();
$msg1->setTopicId($topic->getId());
$msg1->setUserId($user1->getId());
$msg1->save();
$topic = sfSimpleForumTopicPeer::retrieveByPk($topic->getId());
$t->is($topic->getLatestPost()->getId(), $msg1->getId(), 'Newly added message is seen as the topic\'s latest reply');
$t->is($topic->getNbPosts(), 1, 'Adding a first message increments the topic number of posts');
$t->is($topic->getUpdatedAt('U'), $msg1->getCreatedAt('U'), 'Adding a first message changes the topic\'s latest update date to the message creation date');
$forum = sfSimpleForumForumPeer::retrieveByPk($forum->getId());
$t->is($forum->getLatestPost()->getId(), $msg1->getId(), 'Newly added message is seen as the forum\'s latest reply');
$t->is($forum->getNbPosts(), 1, 'Adding a first message increments the forum number of posts');
$t->is($forum->getUpdatedAt('U'), $msg1->getCreatedAt('U'), 'Adding a first message changes the forum\'s latest update date to the message creation date');

sleep(1);
$msg2 = new sfSimpleForumPost();
$msg2->setTopicId($topic->getId());
$msg2->setUserId($user2->getId());
$msg2->save();
$topic = sfSimpleForumTopicPeer::retrieveByPk($topic->getId());
$t->is($topic->getNbPosts(), 2, 'Adding a second message increments the topic number of posts');
$t->is($topic->getNbReplies(), 1, 'A topic can calculate its number of replies based on its number of posts');
$t->is($topic->getUpdatedAt('U'), $msg2->getCreatedAt('U'), 'Adding a second message changes the latest reply date of the topic to the message creation date');
$t->is($topic->getLatestPost()->getAuthorName(), $user2->getUsername(), 'Adding a second message changes the latest reply author name to the message author name');
sleep(1);
$msg3 = new sfSimpleForumPost();
$msg3->setTopicId($topic->getId());
$msg3->setUserId($user3->getId());
$msg3->save();
$topic = sfSimpleForumTopicPeer::retrieveByPk($topic->getId());
$t->is($topic->getNbPosts(), 3, 'Adding a third message increments the topic number of replies');
$t->is($topic->getUpdatedAt('U'), $msg3->getCreatedAt('U'), 'Adding a third message changes the latest reply date of the topic to the message creation date');
$t->is($topic->getLatestPost()->getAuthorName(), $user3->getUsername(), 'Adding a third message changes the latest reply author name to the  message author name');

$t->diag('Updating a message');
sleep(1);
$msg1 = sfSimpleForumPostPeer::retrieveByPk($msg1->getId());
$msg1->setTitle('this is a test');
$msg1->save();
$topic = sfSimpleForumTopicPeer::retrieveByPk($topic->getId());
$t->is($topic->getNbPosts(), 3, 'Updating a message doesn\'t change the topic post count');
$t->is($topic->getUpdatedAt('U'), $msg3->getCreatedAt('U'), 'Updating a message doesn\'t change the topic\'s last update date');
$t->is($topic->getLatestPost()->getId(), $msg3->getId(), 'Updating a message doesn\'t change the topic\'s latest reply');
$forum = sfSimpleForumForumPeer::retrieveByPk($forum->getId());
$t->is($forum->getNbPosts(), 3, 'Updating a message doesn\'t change the forum\'s post count');
$t->is($forum->getUpdatedAt('U'), $msg3->getCreatedAt('U'), 'Updating a message doesn\'t change the forum\'s last update date');
$t->is($forum->getLatestPost()->getId(), $msg3->getId(), 'Updating a message doesn\'t change the forum\'s last reply');

$t->diag('Updating the topic');
sleep(1);
$topic = sfSimpleForumTopicPeer::retrieveByPk($topic->getId());
$topic->setTitle('this is another test');
$topic->save();
$t->is($topic->getNbPosts(), 3, 'Updating a topic doesn\'t change the topic number of replies');
$t->isnt($topic->getUpdatedAt('U'), $msg3->getCreatedAt('U'), 'Updating a topic changes the topic\'s latest update date');
$t->is($topic->getLatestPost()->getId(), $msg3->getId(), 'Updating a topic doesn\'t change the topic\'s latest reply');
$forum = sfSimpleForumForumPeer::retrieveByPk($forum->getId());
$t->is($forum->getLatestPost()->getCreatedAt('U'), $msg3->getCreatedAt('U'), 'Updating the topic doesn\'t change the forum\'s last update date');
$t->is($forum->getLatestPost()->getAuthorName(), $user3->getUsername(), 'Updating the topic doesn\'t change the forum\'s last reply author name');

$t->diag('Deleting a message from the end');
$msg3->delete();
$topic = sfSimpleForumTopicPeer::retrieveByPk($topic->getId());
$t->is($topic->getNbPosts(), 2, 'Deleting a message decrements the topic number of replies');
$t->is($topic->getUpdatedAt('U'), $msg2->getCreatedAt('U'), 'Deleting a message changes the topic\'s latest update date to the latest message creation date');
$t->is($topic->getLatestPost()->getId(), $msg2->getId(), 'Deleting a message changes the topic\'s latest reply');
$forum = sfSimpleForumForumPeer::retrieveByPk($forum->getId());
$t->is($forum->getUpdatedAt('U'), $msg2->getCreatedAt('U'), 'Deleting a message changes the forums\'s latest update date to the latest message creation date');
$t->is($forum->getLatestPost()->getId(), $msg2->getId(), 'Deleting a message changes the forums\'s latest reply');

$t->diag('Deleting a message from the middle');
$msg1->delete();
$topic = sfSimpleForumTopicPeer::retrieveByPk($topic->getId());
$t->is($topic->getNbPosts(), 1, 'Deleting a message decrements the topic number of replies');
$t->is($topic->getUpdatedAt('U'), $msg2->getCreatedAt('U'), 'Deleting a message from the middle doesn\'t change the topic\'s latest update date');
$t->is($topic->getLatestPost()->getId(), $msg2->getId(), 'Deleting a message from the middle doesn\'t change the topic\'s latest reply');
$forum = sfSimpleForumForumPeer::retrieveByPk($forum->getId());
$t->is($forum->getUpdatedAt('U'), $msg2->getCreatedAt('U'), 'Deleting a message from the middle doesn\'t change the forum\'s latest update date');
$t->is($forum->getLatestPost()->getId(), $msg2->getId(), 'Deleting a message from the middle doesn\'t change the forum\'s latest reply');
