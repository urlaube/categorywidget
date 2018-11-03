<?php

  /**
    This is the CategoryWidget plugin.

    This file contains the CategoryWidget plugin. It provides a widget that
    lists all available categories.

    @package urlaube\categorywidget
    @version 0.1a9
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class CategoryWidget extends BaseSingleton implements Plugin {

    // RUNTIME FUNCTIONS

    public static function plugin() {
      $result = null;

      $categories = [];
      if (!getcache(null, $categories, static::class)) {
        callcontent(null, true, true,
                    function ($content) use (&$categories) {
                      $result = null;

                      // check that $content is not hidden
                      if (!istrue(value($content, HidePlugin::HIDDEN))) {
                        // check that $content is not hidden from category
                        if (!istrue(value($content, HidePlugin::HIDDENFROMCATEGORY))) {
                          // check that $content is not a relocation
                          if (null === value($content, RelocatePlugin::RELOCATE)) {
                            // read the category
                            $catvalue = value($content, CATEGORY);
                            if (null !== $catvalue) {
                              $seen = [];

                              $catvalue = explode(SP, $catvalue);
                              foreach ($catvalue as $catvalue_item) {
                                // make sure that only valid characters are contained
                                if (1 === preg_match("~^[0-9A-Za-z\_\-]+$~", $catvalue_item)) {
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
                        }
                      }

                      return null;
                    });

        setcache(null, $categories, static::class);
      }

      if (0 < count($categories)) {
        // sort the categories
        ksort($categories);

        $content = fhtml("<div>".NL);
        foreach ($categories as $key => $value) {
          $metadata = new Content();
          $metadata->set(CATEGORY, $key);

          $content .= fhtml("  <span class=\"glyphicon glyphicon-tag\"></span> <a href=\"%s\">%s</a> (%d)".BR.NL,
                            CategoryHandler::getUri($metadata),
                            $key,
                            $value);
        }
        $content .= fhtml("</div>");

        $result = new Content();
        $result->set(CONTENT, $content);
        $result->set(TITLE,   t("Kategorien", static::class));
      }

      return $result;
    }

  }

  // register plugin
  Plugins::register(CategoryWidget::class, "plugin", ON_WIDGETS);

  // register translation
  Translate::register(__DIR__.DS."lang".DS, CategoryWidget::class);
