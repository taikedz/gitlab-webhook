# Gitlab Merge Request Hook

Scenario:

1. You have code in a Gitlab repository
2. You branch and merge-request back to master
3. Upon merging, you fire off a web integration hook to your target server
4. On the target server, the local copy of the repository is updated

This mini project provides the assets for achieving 3 and 4

* a PHP endpoint to call in the Gitlab integration - deploy this in a web server
* An example of a runner - add it to `/usr/local/bin/runner.sh`, modify it as needed
* A `www-data.sudo` snippet to allow the webserver to run the updates as a different user
    * it is recommended you adapt the user accordingly

## Installing

Example installation steps:

    sudo apt-get update && sudo apt-get install apache2 libapache2-mod-php

    sudo cp runner/runner.sh /usr/local/bin/
    sudo cp configs/www-data.sudo /etc/sudoers.d/
    sudo cp web/*.php /var/www/html/

    # And then edit your deployed /var/www/html/project-mapping.php

## Gitlab setup

* In your project, go to `Settings > Integrations`

* Choose to add a web hook, at the URL where you deployed the listening script `reload.php`

* Choose to only accept merge requests

* Add the token that you set in `project-mapping.php`

* Clone your repository to the location desired, and specified in `project-mapping.php`
