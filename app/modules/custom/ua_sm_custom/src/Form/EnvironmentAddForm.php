<?php

namespace Drupal\ua_sm_custom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

/**
 * Class SiteAddForm.
 *
 * @package Drupal\ua_sm_custom\Form
 */
class EnvironmentAddForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ua_sm_custom_environment_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $node = NULL) {
    $site = Node::load($node);

    // @todo Revisit this when there's more than one platform.
    $platform_ids = \Drupal::entityQuery('node')
      ->condition('type', 'ua_sm_platform')
      ->execute();
    $platform_id = array_pop($platform_ids);

    $build = [
      'title' => [
        '#type' => 'textfield',
        '#title' =>  $this->t('Name'),
        '#size' => 60,
        '#maxlength' => 255,
        '#required' => TRUE,
      ],
      'field_ua_sm_git_reference' => [
        '#type' => 'textfield',
        '#title' =>  $this->t('Git tag/branch'),
        '#size' => 60,
        '#maxlength' => 50,
        '#required' => TRUE,
      ],
      'field_ua_sm_machine_name' => [
        '#type' => 'select',
        '#title' =>  $this->t('Type'),
        '#options' => [
          'dev' => 'DEV',
          'uat' => 'UAT',
          'prd' => 'PROD',
        ],
        '#default_value' => 'dev',
        '#maxlength' => 255,
        '#required' => TRUE,
      ],
      'field_ua_sm_domain_name' => [
        '#type' => 'hidden',
        '#value' => $site->field_ua_sm_domain_name->value,
      ],
      'field_ua_sm_site' => [
        '#type' => 'hidden',
        '#value' => $site->id(),
      ],
      'field_ua_sm_database_password' => [
        '#type' => 'hidden',
        '#value' => \Drupal::service('ua_sm_custom.password')->generate(),
      ],
      'field_ua_sm_platform' => [
        '#type' => 'hidden',
        '#value' => $platform_id,
      ],
      'type' => [
        '#type' => 'hidden',
        '#value' => 'ua_sm_environment',
      ],
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Add'),
      ],
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->cleanValues();
    $input = $form_state->getUserInput();

    $environment = Node::create($input);
    $environment->validate();
    $environment->save();

    drupal_set_message($this->t('Successfully added environment %title.', [
      '%title' => $input['title'],
    ]));

    $form_state->setRedirect(
      'view.ua_sm_site_environments.page_1',
      ['node' => $input['field_ua_sm_site']]
    );
  }

}
