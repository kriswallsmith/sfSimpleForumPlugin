<?php

function forum_breadcrumb($params, $options = array())
{
  if(!$params) return;
  
  $first = true;
  $title = '';
  $id = isset($options['id']) ? $options['id'] : 'forum_navigation';
  $html = '<ul id="'.$id.'">';
  foreach ($params as $step) 
  { 
    $separator = $first ? '' : sfConfig::get('app_sfSimpleForumPlugin_breadcrumb_separator', ' » ');
    $first = false;
    $html .= '<li>'.$separator;
    $title .= $separator;
    if(is_array($step))
    {
      $html .= link_to($step[0], $step[1]);
      $title .= $step[0];
    }
    else
    {
      $html .= $step;
      $title .= $step;
    }
    $html .= '</li>';
  }
  $html .= '</ul>';
 
  sfContext::getInstance()->getResponse()->setTitle($title); 
  return $html;
}