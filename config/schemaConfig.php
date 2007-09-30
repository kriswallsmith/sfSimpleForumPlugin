<?php

function start_schema()
{
  ob_start();
}

function end_schema()
{
  $tables = array(
    'connection'       => 'propel',
    'user_table'       => 'sf_guard_user',
    'user_id'          => 'id',
    'user_class'       => 'sfGuardUser',
    'category_table'   => 'sf_simple_forum_category',
    'forum_table'      => 'sf_simple_forum_forum',
    'topic_table'      => 'sf_simple_forum_topic',
    'post_table'       => 'sf_simple_forum_post',
    'topic_view_table' => 'sf_simple_forum_topic_view',
  );

  $custom_fields = array();

  // Check custom project values in my_project/config/sfBlogPlugin.yml
  if(is_readable($config_file = sfConfig::get('sf_config_dir').'/sfSimpleForumPlugin-schema-custom.yml'))
  {
    $user_config = sfYaml::load($config_file);
    if(isset($user_config['tables']))
    {
      $tables = array_merge($tables, $user_config['tables']);
    }

    if(isset($user_config['custom_fields']))
    {
      $custom_fields = $user_config['custom_fields'];
    }

  }

  $yaml_schema = ob_get_clean();
  foreach ($tables as $key => $value)
  {
    $yaml_schema = str_replace('%'.$key.'%', $value, $yaml_schema);
  }

  $schema = sfYaml::load($yaml_schema);

  $schema[$tables['connection']] = sfToolkit::arrayDeepMerge($schema[$tables['connection']], $custom_fields);

  return $schema;
}