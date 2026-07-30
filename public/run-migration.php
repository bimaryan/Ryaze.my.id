<?php
chdir('..');
system('php artisan migrate --force');
echo 'Migrated!';
