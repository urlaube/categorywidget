<?php

  /**
    This is the CategoryWidget plugin.

    This file contains the CategoryWidget plugin. It provides a widget that lists all available categories.

    @package urlaube\categorywidget
    @version 0.1a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("CategoryWidget")) {
    class CategoryWidget implements Plugin {

      // RUNTIME FUNCTIONS

      public static function plugin() {
        $result = new Content();

        $categories = array();

        File::loadContentDir(USER_CONTENT_PATH, true,
                             function ($content) use (&$categories) {
                               $result = null;

                               // check that $content is not hidden
                               if (!ishidden($content)) {
                                 // read the category
                                 if ($content->isset(CATEGORY)) {
                                   $seen = array();

                                   $catvalue = explode(SP, $content->get(CATEGORY));
                                   foreach ($catvalue as $catvalue_item) {
                                     // make sure that only valid characters are contained
                                     if (1 === preg_match("@^[0-9A-Za-z\_\-]+$@", $catvalue_item)) {
                                       $catvalue_item = strtolower($catvalue_item);

                                       // only count each category once per content
                                       if (!isset($seen[$catvalue_item])) {
                                         $seen[$catvalue_item] = null;

                                         if (isset($categories[$catvalue_item])) {
                                           $categories[$catvalue_item]++;
                                         } else {
                                           $categories[$catvalue_item] = 1;
                                         }
                                       }
                                     }
                                   }
                                 }
                               }

                               return null;
                             },
                             true);

        // sort the categories
        ksort($categories);

        $content = "<div>".NL;
        foreach ($categories as $key => $value) {
          $content .= "  <span class=\"glyphicon glyphicon-tag\"></span> <a href=\"".
                      html(CategoryHandler::getUri(array(CATEGORY => $key, PAGE => 1))).
                      "\">".html($key)."</a> (".html($value).")".BR.NL;
        }
        $content .= "</div>";

        $result->set(TITLE,   "Kategorien");
        $result->set(CONTENT, $content);

        return $result;
      }

    }

    // register plugin
    Plugins::register("CategoryWidget", "plugin", ON_WIDGETS);
  }

