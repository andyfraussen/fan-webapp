# Fan Webapp
Give fans the opportunity to order items
## Installation
Instructions on how to get the project up and running on your local machine for development and testing purposes.
1. composer install 
2. touch db
3. php bin/console doctrine:migrations:migrate
4. php bin/console doctrine:fixtures:load
5. php bin/console ImportFanCsvCommand fans.csv
6. symfony server:start