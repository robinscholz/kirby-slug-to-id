<?php
Kirby::plugin('studioscholz/slug-to-id', [
  'routes' => function ($kirby) {
    return [
      [
        'pattern' => 'slug-to-id',
        'method'  => 'GET',
        'action'  => function () {
          $base = dirname(__DIR__, 3);
          // Include languages
          $languageFiles = glob($base . '/site/languages' . '/*.php');
          $languages = array();
          foreach ($languageFiles as $language) {
            $languages[] = include $language;
          }
          $currentLanguage = kirby()->language();
          // Define data
          $data = [];
          // Merge pages
          $allPages = kirby()->site()->index();
          // Translate
          foreach($allPages as $entry) {
            foreach($languages as $language) {
              $parents = $entry->parents()->flip();
              $slug = '/';
              // Add language code
              $slug .= $language['code'] != $currentLanguage ? $language['code'] : '';
              // Add slash for language code
              if($language['code'] != $currentLanguage && $entry->slug($language['code']) != 'index') {
                $slug .= '/';
              }
              // Add all parents’ slugs
              foreach($parents as $parent) {
                $slug .= $parent->slug($language['code']) . '/';
              }
              // Add page slug
              if($entry->slug($language['code']) != 'index') {
                $slug .= $entry->slug($language['code']);
              }
              // Add entry
              $data[$language['code']][$slug] = $entry->id();
            }
          }
          return [
            'status' => 'ok',
            'data'   => $data
          ];
        }
      ]
    ];
  }
]);