Shepherd
========

The University of Adelaide Shepherd provides an administration UI for provisioning web sites and managing user access. 

## Requirements

#### ATTN: macOS users

You can use Docker Toolbox or Docker for Mac with this project. Both are supported in the `dsh` script. Both environments
have [issues with filesystem speed](https://github.com/docker/for-mac/issues/77). Docker Toolbox ( `docker-machine` ) is using `docker-machine-nfs` to address the issue by using NFS. 
This has improved speed massively for users. A similar solution for `docker for mac` called [d4m nfs](https://github.com/IFSight/d4m-nfs) is available and essentially does the same 
thing, however this has not been implemented in the `dsh` script. If using `docker for mac` you will experience speed and peformance issues
performing tasks large writes inside the container. 


* [Docker](https://www.docker.com/community-edition) ( **optional** macOS - docker for mac) 
* [Docker Toolbox](https://www.docker.com/products/docker-toolbox) - ( macOS only )
* PHP installed on your host machine
* [Composer](https://getcomposer.org/)
* [Minishift](https://github.com/minishift/minishift) - for Orchestration tasks.


```bash
# macOS installation script
# this will ensure that all deps for docker toolbox/machine development are installed.
./dsh install_tools

# setup 
composer install

# bring up containers, runs any configuration tasks
./dsh start

# Configure dsnmasq
./dsh setup_dnsmasq

# Drop into a utility shell ( this creates a ssh-agent container for macOS )
./dsh shell

# bring down containers, remove network and volumes.
./dsh purge
```

### Setting up Minishift orchestration

Documentation can be found on [Google Drive](https://docs.google.com/document/d/1ZeypugCthqFfHiLXCe6XyEJ9kO0rWhTzLb3bQa3KLqY/edit#heading=h.e4erzx509ekv)

Once you have configured your minishift openshift environment do the following : 
- Go to the orchestration settings in the configuration page. 
- Select openshift as the provider. 
- Select the openshift tab and update the details.

Test the API configuration by triggering an environment by editing an existing site definition.


### Installing via Drush

```drush
# Drop into the utility shell
./dsh shell
# Run the drush site install
# set user:password
drush -r web si shepherd --db-url=mysql://user:password@db/drupal --account-name=admin --account-pass=password --site-name=Shepherd -y
# Remove previous install settings and files {from host}
sudo rm -rf web/sites/default/files/
sudo rm -f web/sites/default/settings.php
sudo rm -f web/sites/default/services.yml
```

### Updating scaffolds 

Anything using the `master` branch is going to be cached by composer. To unsure you get the latest, clear the composer cache
```bash
composer clear-cache
# update - look at the composer.json > "scripts" to see the commands that are run during an update
composer update
```


### Additional notes and troubleshooting

- mysql host is `db`, database is `drupal`, user is `user` and password is `password`
- You may need to make `dsh` executable after a `composer update` - `chmod +x dsh`
- If performing a site install using `drush` ensure you have removed the `web/sites/default/files/` `web/sites/default/settings.php` and 
`web/sites/default/services.yml`
- Purging doesn't remove the `ssh_agent` or `nginx_proxy` there will be some volumes that will continue living. You will need to 
occasionally remove these : `docker volume ls` and `docker volume rm ${vol_name}` - to remove dangling volumes.
- Updating `/web/sites/default/settings.php` using `composer update` requires you to remove the file before. 


#### Exporting configuration 

When exporting config always to remember to clean the `yml` files of `uuid` and config hashes.

```bash
# You can run this in the container.
# Replace {config_dir} with the destination you exported config to.
sed -i '/^uuid: .*$/d' /code/web/{config_dir}*.yml
```

```bash
# Regex to replace config hashes:
# case insensitive and multi-line
/\_core\:.*\n.*default\_config\_hash:.*$/im
```

To diff the configuration : 
```bash
# you can run this on the container 
diff -N -I "   - 'file:.*" -qbr {old_config_path} {new_config_path}
```

