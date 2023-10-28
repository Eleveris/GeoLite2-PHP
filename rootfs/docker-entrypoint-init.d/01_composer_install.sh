#!bin/sh
echo "Running Composer Dependencies Installation"
if [ -f /opt/app/composer.json ] && [ ! -d /opt/app/vendor ] && [ -f /opt/app/index.php ]; then { \
  if [ -f /opt/app/composer.lock ]; then rm /opt/app/composer.lock; fi
  mv /opt/app/index.php /opt/app/index.php2;
  cd /opt/app;
  composer install;
  mv /opt/app/index.php2 /opt/app/index.php;
}; fi
echo "Composer Dependencies Installation Finished"