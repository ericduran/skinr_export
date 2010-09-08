<?php
// $Id$

/**
 * @file
 * A custom Ctools Export UI class for Skinr Settings.
 */

/**
 * Customizations of the Skinr Export UI.
 */
class skinr_export_ui extends ctools_export_ui {

  /**
   * hook_menu() entry point.
   *
   * Child implementations that need to add or modify menu items should
   * probably call parent::hook_menu($items) and then modify as needed.
   */
  function hook_menu(&$items) {
    parent::hook_menu($items);
    
    //We want to unset all the default skinr ui, as we don't want to reimplemet it
    unset($items['admin/build/skinr/add']);
  }


  /**
   * Alter the preset list form defined by the base class.
   */
  function list_form(&$form, &$form_state) {
    parent::list_form($form, $form_state);
    
    //Pop out the submit and reset button
    $submit = array_pop($form['bottom row']);
    $reset = array_pop($form['bottom row']);
    
    
    $themes = list_themes();
    ksort($themes);

    $options = array();
    $options['all'] = 'all';
    foreach ($themes as $theme) {
      if (!$theme->status) {
        continue;
      }
      $options[$theme->name] = $theme->info['name'];
    }
    
    $form['bottom row']['theme'] = array(
      '#type' => 'select',
      '#title' => t('Theme'),
      '#options' => $options,
      '#default_value' => 'all',
    );
  
    // Type filter.
    $config = skinr_fetch_config();

    $type_filter = array();
    $type_filter['all'] = 'all';
    foreach ($config as $type => $data) {
      $type_filter[$type] = $type;
    }
    
    $form['bottom row']['type'] = array(
      '#type' => 'select',
      '#title' => t('type'),
      '#options' => $type_filter,
      '#default_value' => 'all',
    );


    array_push($form['bottom row'], $reset);
    array_push($form['bottom row'], $submit);
  }
  
  /**
   * Determine if a row should be filtered out.
   *
   * This handles the default filters for the export UI list form. If you
   * added additional filters in list_form() then this is where you should
   * handle them.
   *
   * @return
   *   TRUE if the item should be excluded.
   */
  function list_filter($form_state, $item) {
    parent::list_filter($form_state, $item);
    
    if ($form_state['values']['theme'] != 'all' && $form_state['values']['theme'] != $item->theme) {
      return TRUE;
    }
    
    if ($form_state['values']['type'] != 'all' && $form_state['values']['type'] != $item->module) {
      return TRUE;
    }
  }
  
  
  /**
   * Provide the table header.
   *
   * If you've added columns via list_build_row() but are still using a
   * table, override this method to set up the table header.
   */
  function list_table_header() {
    
    $header = array();
    if (!empty($this->plugin['export']['admin_title'])) {
      $header[] = array('data' => t('Title'), 'class' => 'ctools-export-ui-title');
    }

    $header[] = array('data' => t('Skinr-Id'), 'class' => 'ctools-export-ui-name');
    $header[] = array('data' => t('Storage'), 'class' => 'ctools-export-ui-storage');
    $header[] = array('data' => t('Operations'), 'class' => 'ctools-export-ui-operations');
    $header[] = array('data' => t('Theme'), 'class' => 'ctools-export-ui-theme');
    $header[] = array('data' => t('Module'), 'class' => 'ctools-export-ui-theme');

    return $header;
  }
  
  
  /**
   * Build a row based on the item.
   *
   * By default all of the rows are placed into a table by the render
   * method, so this is building up a row suitable for theme('table').
   * This doesn't have to be true if you override both.
   */
  function list_build_row($item, &$form_state, $operations) {
    //Display the clone before we pass it to the parent
    unset($operations['clone']);
    
    //Edit the 'edit' link to used skinr's edit link
    if ($item->module == 'page') {
      $operations['edit']['href']   = 'admin/build/skinr/rule/edit/'. $item->sid;
    }
    else {
      $operations['edit']['href'] = 'admin/build/skinr/edit/nojs/'. $item->module .'/'. $item->sid;
    }
      
    parent::list_build_row($item, $form_state, $operations);
    
    
    $name = $item->{$this->plugin['export']['key']};
    
    $this->rows[$name]['data'][] = array('data' => check_plain($item->theme), 'class' => 'skinr-export-ui-theme');
    $this->rows[$name]['data'][] = array('data' => check_plain($item->module), 'class' => 'skinr-export-ui-module');
  
  }
}