# Hours Tracker

## Info
This laravel application is designed to track time recorded against a project, with the project
having a set amount of time available for use.

The general break down of the application components:
1. A project represents a distinct group of work items, orders, and time entries.
2. A project will have orders - these are the amount of hours that the project customer has authorized
for use the project.
3. A project will have one or more tasks, called work items.
4. Each work item will have one or more time entries recorded against it.
5. There are 5 types of users:
   1. Super Admin - unrestricted access (no permissions checks).
   2. Admin - complete access.
   3. Read Only Admin - ready only access to everything.
   4. Customer - ready only access to any project assigned as the customer. They may run reports for said projects.
   5. Engineer - access to any project assigned as an engineer but can only track time to work items assigned to them.
   6. Project Manager - access to any project assigned as a project manager and can create/edit work items and time entries.

## Application Requirements
* PHP: 8.2+
* Node: 18+
* MySQL: 8+

## Local Setup

Clone the project from `git clone https://github.com/robertdeboer/tracker.git`

Install dependencies using both composer and npm
`composer install`
`npm ci`

Initialize the application
`cp .env.example .env`
`php artisan key:generate`

Build the UI
`npm run build`

Create a local environment via docker by running
`./vendor/bin/sail up -d`

Initialize the database
`./vendor/bin/sail artisan migrate`

Initialize the system permissions
`./vendor/bin/sail artisan app:permissions`

Access the application at `http://localhost`

For a fresh install, register a user, and they will automatically be
registered as a Super Admin. Any other users may be added manually via
the `System` -> `Users` table.

