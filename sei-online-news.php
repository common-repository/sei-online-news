<?php
/*
Plugin Name: sei online news
Plugin URI: http://wordpress.org/extend/plugins/sei-online-news/
Description: Adds a customizeable widget which displays the latest news by http://www.sei-online.de/
Version: 0.1
Author: Frank Kugler
Author URI: http://www.sei-online.de/
License: GPL3
*/

function seionlinenews()
{
  $options = get_option("widget_seionlinenews");
  if (!is_array($options)){
    $options = array(
      'title' => 'sei online news',
      'news' => '5',
      'chars' => '30'
    );
  }

  // RSS Objekt erzeugen 
  $rss = simplexml_load_file( 
  'http://www.sei-online.de/feed/'); 
  ?> 
  
  <ul> 
  
  <?php 
  // maximale Anzahl an News, wobei 0 (Null) alle anzeigt
  $max_news = $options['news'];
  // maximale Länge, auf die ein Titel, falls notwendig, gekürzt wird
  $max_length = $options['chars'];
  
  // RSS Elemente durchlaufen 
  $cnt = 0;
  foreach($rss->channel->item as $i) { 
    if($max_news > 0 AND $cnt >= $max_news){
        break;
    }
    ?> 
    
    <li>
    <?php
    // Titel in Zwischenvariable speichern
    $title = $i->title;
    // Länge des Titels ermitteln
    $length = strlen($title);
    // wenn der Titel länger als die vorher definierte Maximallänge ist,
    // wird er gekürzt und mit "..." bereichert, sonst wird er normal ausgegeben
    if($length > $max_length){
      $title = substr($title, 0, $max_length)."...";
    }
    ?>
    <a href="<?=$i->link?>"><?=$title?></a> 
    </li> 
    
    <?php 
    $cnt++;
  } 
  ?> 
  
  </ul>
<?php  
}

function widget_seionlinenews($args)
{
  extract($args);
  
  $options = get_option("widget_seionlinenews");
  if (!is_array($options)){
    $options = array(
      'title' => 'sei online news',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  seionlinenews();
  echo $after_widget;
}

function seionlinenews_control()
{
  $options = get_option("widget_seionlinenews");
  if (!is_array($options)){
    $options = array(
      'title' => 'sei online news',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  if($_POST['seionlinenews-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['seionlinenews-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['seionlinenews-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['seionlinenews-CharCount']);
    update_option("widget_seionlinenews", $options);
  }
?> 
  <p>
    <label for="seionlinenews-WidgetTitle">Widget Title: </label>
    <input type="text" id="seionlinenews-WidgetTitle" name="seionlinenews-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="seionlinenews-NewsCount">Max. News: </label>
    <input type="text" id="seionlinenews-NewsCount" name="seionlinenews-NewsCount" value="<?php echo $options['news'];?>" />
    <br /><br />
    <label for="seionlinenews-CharCount">Max. Characters: </label>
    <input type="text" id="seionlinenews-CharCount" name="seionlinenews-CharCount" value="<?php echo $options['chars'];?>" />
    <br /><br />
    <input type="hidden" id="seionlinenews-Submit"  name="seionlinenews-Submit" value="1" />
  </p>
  
<?php
}

function seionlinenews_init()
{
  register_sidebar_widget(__('sei online news'), 'widget_seionlinenews');    
  register_widget_control('sei online news', 'seionlinenews_control', 300, 200);
}
add_action("plugins_loaded", "seionlinenews_init");
?>
