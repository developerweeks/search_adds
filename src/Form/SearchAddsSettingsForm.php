<?php

namespace Drupal\search_adds\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form to configure Search Adds settings.
 */
class SearchAddsSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'search_adds_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'search_adds.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('search_adds.settings');

    $form['toptext'] = [
      '#type' => 'markup',
      '#markup' => '<p>Here is where you can change the responses to certain trigger words in the search results</p>',
    ];

    $adds_count = $form_state->get('adds_count');
    if ($adds_count < 1) {
      $adds_count = $config->get('counted');
      $form_state->set('adds_count', $adds_count);
    }

    $form['suggestions'] = [
      '#type' => 'fieldset',
      '#title' => t('Suggestions'),
      '#prefix' => '<div id="suggs_wrapper">',
      '#suffix' => '</div>',
    ];
    for ($i = 1; $i <= $adds_count; $i++) {
      $form['suggestions']['line'][$i] = [
        '#type' => 'details',
        '#title' => $this->t('Suggestion @i', ['@i' => $i]),
        '#collapsible' => TRUE,
        '#open' => TRUE,
      ];
      // Fieldnames need to be unique for form value handling.
      $form['suggestions']['line'][$i]['trigger' . $i] = [
        '#type' => 'textfield',
        '#title' => $this->t('Keyword or Trigger'),
        '#default_value' => $config->get('trigger_' . $i),
        '#description' => $this->t('if a user searches for this'),
        '#required' => TRUE,
      ];
      $form['suggestions']['line'][$i]['response' . $i] = [
        '#type' => 'textfield',
        '#title' => $this->t('Response'),
        '#default_value' => $config->get('response_' . $i),
        '#description' => $this->t('Show this to the user.'),
        '#required' => TRUE,
      ];

    }
    // Adds "Add another item" button.
    $form['suggestions']['add_item'] = [
      '#type' => 'submit',
      '#value' => t('Add more suggestions'),
      '#submit' => ['::addServer'],
      '#ajax' => [
        'callback' => '::changeServer',
        'method' => 'replace',
        'wrapper' => 'suggs_wrapper',
        'effect' => 'fade',
      ],
      '#limit_validation_errors' => [],
    ];

    // Button to remove last item.
    if ($adds_count > 1) {
      $form['suggestions']['remove_item'] = [
        '#type' => 'submit',
        '#value' => t('Remove last suggestion'),
        '#submit' => ['::removeServer'],
        '#ajax' => [
          'callback' => '::changeServer',
          'method' => 'replace',
          'wrapper' => 'suggs_wrapper',
          'effect' => 'fade',
        ],
        '#limit_validation_errors' => [],
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $adds_count = $form_state->get('adds_count');

    // Save the configuration changes.
    $search_adds_config = \Drupal::service('config.factory')->getEditable('search_adds.settings');
    $search_adds_config->set('counted', $adds_count);
    for ($i = 1; $i <= $adds_count; $i++) {
      // Values are flattened in array with field name as key.
      $search_adds_config->set('trigger_' . $i, $values['trigger' . $i]);
      $search_adds_config->set('response_' . $i, $values['response' . $i]);

      $search_adds_config->save();
    }
    parent::submitForm($form, $form_state);
  }

  /**
   * Add a SMTP server.
   */
  public function addServer(array &$form, FormStateInterface $form_state) {
    $n = $form_state->get('adds_count') ? $form_state->get('adds_count') : 1;
    $form_state->set('adds_count', ++$n);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Remove a SMTP server.
   */
  public function removeServer(array &$form, FormStateInterface $form_state) {
    $n = $form_state->get('adds_count');
    $form_state->set('adds_count', --$n);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Change a SMTP server.
   */
  public function changeServer(array &$form, FormStateInterface $form_state) {
    return $form['suggestions'];
  }

}
