<?php

/**
 * Define this Export UI plugin.
 */
$plugin = array(
  'schema' => 'skinr',
  'access' => 'administer skinr',
  'menu' => array(
    'menu item' => 'skinr',
    'menu prefix' => 'admin/build',
    'menu title' => 'Skinr',
    'menu description' => 'Administer Skinr Skins.',
  ),

  'title singular' => t('skin'),
  'title plural' => t('skins'),
  'title singular proper' => t('Skinr skin'),
  'title plural proper' => t('Skinr skins'),
  
  'handler' => array(
    'class' => 'skinr_export_ui',
    'parent' => 'ctools_export_ui',
  ),
);

