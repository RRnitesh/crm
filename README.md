a basic crm system built with Laravel and Express.js.

•	Git repository link


•	How to run the project 
1. inside crm folder: laravel project is in backend folder and node project in node-serive folder.
2. run 
  php artisan migrate --seed in backend folder
  php artisan serve in backend folder 
  php artisan schedule:work in backend folder
  node index.js in node service folder.
3. you can find the postman collection in the root folder of the project named "postman_collection.json". Import it to test the APIs.


for login, use the following credentials for admin user.

admin auth is 
'name' => 'admin',
'email' => 'admin@gmail.com',
'password' => ('123456'),

initially, postman collection has 2 variables {{baseurl}} and {{token}}. {{baseurl}} is set to localhost:8000/api/v1/ and 
breartoken {{token}} should be replaced with the token received from the login API response.


•	Key design decisions / Use cases and Features implemented in the project

1. User authentication (login, logout) powered by Laravel Sanctum.
2. Ticket creation, comment creation, status update logic and pagination for tickets.
3. Implemented SLA(service Level Agreement) logic to high proiority tickets that must be responded in certain time frame
(check config/sla.php configuration).
4. Fire up the worker to run SLA breach check every minute using Laravel's task scheduling.
5. In case of SLA breach, called node-service to log the ids.



•	What they would improve with more time 
1. i would add rbac(role based access control) to the system, implement role and permission on controller action level,
such that with in support team they would have restriction.
2. i would explore more on node js part and implement appropriate business logic.