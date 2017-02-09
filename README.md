# Project-2.2
# This is a project for the Hanze University.
The TSWS receives localized weather data from weather stations from all over the world. The data is received via a collection server. Currently, this server is set up to receive up to a maximum of 800 weather stations at the same time. For the further use of this data, a distribution server is used. This server provides the clients with the requested data for their research.
Both servers are lacking resource overhead with the use of these applications. The resource allocation provided quite a few difficult design choices. But the provided TSWS still provided the data as requested by the client.

The University also asked for the calculation of two specific datasets:
A calculation of the humidity values for all the weather stations within Serbia. The data should be available at intervals of 10 seconds per station. The data should be represented in a graph for the past 60 minutes. It would be valuable if it is possible to zoom in within the graph, so that even subtle changes are easy to recognize. The primary goal for these values is to help academics in the research for fungi and other trails involving humidity dependent variables.
A representation of the 5 weather stations, within the Balkan area, that have the highest visibility distance. The visibility ranking should be calculated per day, from midnight onwards. A permanent history of these values should be available at all times. If the data for the current day is requested, the values should be calculated at once for all the measurements until that point of the day.

#Installation:
Website:  
  - Make sure your system meets all the requirements of laravel https://laravel.com/docs/5.4/installation  
  - Pull the master brange using git  
  - Create a database  
  - Import database/thema2.2.sql in the database  
  - Optional:  
    - You can also run php artisan migrate (but some migrations are missing)  
    - After this seed the database with data: php artisan db:seed  
  - Change the .env.example to .env and change the settings  
  - Generate a key using: php artisan key:generate  
  - Run the website using: php artisan serve  
  - Register an test account at /register  
  - Login and view the website  

Development:
In the website folder  
  - Download composer and set it in your enviroment variables.  
  - composer install  
  - composer update
