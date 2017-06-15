<?php

namespace Drupal\shp_orchestration;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Interface OrchestrationProviderInterface.
 *
 * @package Drupal\shp_orchestration
 */
interface OrchestrationProviderInterface extends PluginInspectionInterface {

  /**
   * Creates artifacts in orchestration provider based on Shepherd distribution.
   *
   * @param string $name
   *   Name of the distribution.
   * @param string $builder_image
   *   An s2i-enabled image to use to build (and run) the source code.
   * @param string $source_repo
   *   Source code git repository.
   * @param string $source_ref
   *   Source code git ref, defaults to 'master'.
   * @param string|null $source_secret
   *   The secret to use when pulling and building the source git repository.
   *
   * @return bool
   *   Returns true if succeeded.
   */
  public function createdDistribution(
    string $name,
    string $builder_image,
    string $source_repo,
    string $source_ref = 'master',
    string $source_secret = NULL
  );

  /**
   * Updates artifacts in orchestration provider based on Shepherd distribution.
   *
   * @param string $name
   *   Name of the distribution.
   * @param string $builder_image
   *   An s2i-enabled image to use to build (and run) the source code.
   * @param string $source_repo
   *   Source code git repository.
   * @param string $source_ref
   *   Source code git ref, defaults to 'master'.
   * @param string|null $source_secret
   *   The secret to use when pulling and building the source git repository.
   *
   * @return bool
   *   Returns true if succeeded.
   */
  public function updatedDistribution(
    string $name,
    string $builder_image,
    string $source_repo,
    string $source_ref = 'master',
    string $source_secret = NULL
  );

  /**
   * Deletes an existing distribution.
   *
   * @param string $name Name of distribution.
   * @return mixed
   */
  public function deletedDistribution($name);

  /**
   * @param string $distribution_name
   *   Name of the distribution.
   * @param string $site_name
   *   Name of the site.
   * @param string $environment_name
   *   Name of the environment.
   * @param string $environment_id
   *   Unique id of the environment.
   * @param string $builder_image
   *   An s2i-enabled image to use to build (and run) the source code.
   * @param string $source_repo
   *   Source code git repository.
   * @param string $source_ref
   *   Source code git ref, defaults to 'master'.
   * @param string|null $source_secret
   *   The secret to use when pulling and building the source git repository.
   *
   * @return bool
   *   Returns true if succeeded.
   */
  public function createdEnvironment(
    string $distribution_name,
    string $site_name,
    string $environment_name,
    string $environment_id,
    string $builder_image,
    string $source_repo,
    string $source_ref = 'master',
    string $source_secret = NULL
  );

  /**
   * @return mixed
   */
  public function updatedEnvironment();

  /**
   * Delete the environment in the orchestration provider.
   *
   * @param string $distribution_name
   * @param string $site_name
   * @param string $environment_name
   * @param string $environment_id
   *
   * @return mixed
   */
  public function deletedEnvironment(
    string $distribution_name,
    string $site_name,
    string $environment_name,
    string $environment_id
  );

  /**
   * @return mixed
   */
  public function createdSite();

  /**
   * @return mixed
   */
  public function updatedSite();

  /**
   * @return mixed
   */
  public function deletedSite();

  /**
   * Retrieves the metadata on a stored secret.
   *
   * @param string $name
   *    Secret name.
   * @param string $key
   *    Optional key name to return.
   *
   * @return array|string|bool
   *   Returns the secret array if successful, the value of the key if set, or
   *   false.
   */
  public function getSecret(string $name, string $key = NULL);

  /**
   * Creates a secret.
   *
   * @param string $name
   *    The name of the secret to be stored.
   * @param array $data
   *    Key value array of secret data.
   *
   * @return array|bool
   *    Returns the secret array if successful, otherwise false.
   */
  public function createSecret(string $name, array $data);

  /**
   * Updates a secret.
   *
   * @param string $name
   *    The name of the secret to be updated.
   * @param array $data
   *    Key value array of secret data.
   *
   * @return mixed
   *    Returns the secret metadata if successful.
   */
  public function updateSecret(string $name, array $data);

  /**
   * Generates a deployment name from Shepherd entities.
   *
   * @param string $distribution_name
   *   Name of the distribution.
   * @param string $site_name
   *   Name of the site.
   * @param string $environment_name
   *   Name of the environment.
   * @param string $environment_id
   *   Unique id of the environment.
   *
   * @return string
   *   Returns the generated deployment name.
   */
  public static function generateDeploymentName(
    string $distribution_name,
    string $site_name,
    string $environment_name,
    string $environment_id
  );

}
