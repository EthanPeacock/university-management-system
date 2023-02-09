# University Management System

A database system with a simple web-based administrative interface. The database has been designed to store all information on a University and its courses, going to the level of data on timetabling in particular rooms.

The designs for the database can be found in `ERD.png`

The frontend was not the focus for this project, instead focused on relational database techniques/principles. The frontend could be expanded to include a student view for there course info, timetable, ...

## Environment Variables

To run this project with the datbase, you must configure the database credentials.

Within `frontend\db-connect-inc.php` the following must be adapted,

```php
$db = new PDO('mysql:host=localhost; dbname=unisys;','','');
```

to,

```php
$db = new PDO('mysql:host=localhost; dbname=unisys;','username','password');
```
## Run Locally

__REQUIRED:__ XAMPP ([Download](https://www.apachefriends.org/download.html))

Clone the project into `{xampp install folder}\htdocs\`

```bash
  git clone https://github.com/EthanPeacock/university-management-system
```

Launch XAMPP and start Apache and MySQL. Click `admin` on the MySQL row.

Create a new database called `unisys`.

Locate the `database` folder within the cloned project folder. Import the `db.sql` file into the newly created database.

Sample data can now also be important via the same method, using the files in `sample-data`.

Finally, open a browser and go to `localhost/univeristy-management-system`.