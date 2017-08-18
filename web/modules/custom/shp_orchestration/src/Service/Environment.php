<?php

namespace Drupal\shp_orchestration\Service;

use Drupal\node\NodeInterface;
use Drupal\shp_orchestration\OrchestrationProviderPluginManager;

/**
 * Class Environment.
 * @package Drupal\shp_orchestration\Service
 */
class Environment extends EntityBase {

  /**
   * The Shepherd configuration service.
   *
   * @var \Drupal\shp_orchestration\Service\Configuration
   */
  protected $configuration;

  /**
   * Shepherd constructor.
   * @param \Drupal\shp_orchestration\OrchestrationProviderPluginManager $orchestrationProviderPluginManager
   * @param \Drupal\shp_orchestration\Service\Configuration $configuration
   */
  public function __construct(OrchestrationProviderPluginManager $orchestrationProviderPluginManager, Configuration $configuration) {
    parent::__construct($orchestrationProviderPluginManager);
    $this->configuration = $configuration;
  }

  /**
   * Tell the active orchestration provider an environment was created.
   *
   * @param \Drupal\node\NodeInterface $node
   * @return bool
   */
  public function created(NodeInterface $node) {
    if (isset($node->field_shp_site->target_id)) {
      $site = $node->get('field_shp_site')
        ->first()
        ->get('entity')
        ->getTarget()
        ->getValue();

      if (isset($site->field_shp_distribution->target_id)) {
        $distribution = $site->get('field_shp_distribution')
          ->first()
          ->get('entity')
          ->getTarget()
          ->getValue();
      }
    }
    if (!isset($distribution) || !isset($site)) {
      return FALSE;
    }

    $cron_jobs = [];
    foreach ($node->field_shp_cron_jobs as $job) {
      $cron_jobs[$job->key] = $job->value;
    }

    $deployment_name = $this->orchestrationProviderPlugin::generateDeploymentName(
      $distribution->getTitle(),
      $site->field_shp_short_name->value,
      $node->id()
    );

    // Generate an auth token and add it to the secret associated with the
    // environment. Create the secret if it doesn't exist.
    // @todo Replace with generated Shepherd auth token.
    $shepherd_token = 'super-secret-token';
    if ($env_secret = $this->orchestrationProviderPlugin->getSecret($deployment_name)) {
      $secret_result = $this->orchestrationProviderPlugin->updateSecret(
        $deployment_name,
        array_merge($env_secret, ['SHEPHERD_TOKEN' => $shepherd_token])
      );
    }
    else {
      $secret_result = $this->orchestrationProviderPlugin->createSecret(
        $deployment_name,
        ['SHEPHERD_TOKEN' => $shepherd_token]
      );
    }
    if (!$secret_result) {
      // @todo Handle errors.
      return FALSE;
    }

    // Get environment variables and secrets.
    $env_vars = $this->configuration->getEnvironmentVariables($node);
    $secrets = $this->configuration->getSecrets($node);

    return $this->orchestrationProviderPlugin->createdEnvironment(
      $distribution->getTitle(),
      $site->field_shp_short_name->value,
      $site->id(),
      $node->id(),
      $node->toUrl('canonical', ['absolute' => TRUE])->toString(),
      $distribution->field_shp_builder_image->value,
      $distribution->field_shp_git_repository->value,
      $node->field_shp_git_reference->value,
      $distribution->field_shp_build_secret->value,
      $env_vars,
      $secrets,
      $cron_jobs
    );
  }

  /**
   * Tell the active orchestration provider an environment was updated.
   *
   * @param \Drupal\node\NodeInterface $node
   * @return bool
   */
  public function updated(NodeInterface $node) {
    // @todo implement me.
    return TRUE;
  }

  /**
   * Tell the active orchestration provider an environment was deleted.
   *
   * @param \Drupal\node\NodeInterface $node
   * @return bool
   */
  public function deleted(NodeInterface $node) {
    $site = $node->get('field_shp_site')
      ->first()
      ->get('entity')
      ->getTarget()
      ->getValue();

    if (isset($site->field_shp_distribution->target_id)) {
      $distribution = $site->get('field_shp_distribution')
        ->first()
        ->get('entity')
        ->getTarget()
        ->getValue();
    }
    if (!isset($distribution) || !isset($site)) {
      return FALSE;
    }

    return $this->orchestrationProviderPlugin->deletedEnvironment(
      $distribution->title->value,
      $site->field_shp_short_name->value,
      $node->id()
    );
  }

}