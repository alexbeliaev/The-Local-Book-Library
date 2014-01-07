The-Local-Book-Library
Assume you have a large book collection in some folders, and you would like to create a database of your books, so that you can know the books description and have an easy way to find the right book, by an author, title, or a keyword.

The books are added to the database without changing they names or transferring them to some other location. This is not "invasive" procedure which preserves the original file structure.


Description
Purpose
1. Create a list of all the books in the given folder.
2. For each book compute MD5 HASH.
3. Using HASHes find book description in LibGen database.
4. Create a MySQL table, containing description of all the books that have been found in the LibGen database.
5. Get access to your collection through the web browser interface and the local HTTP web server.

Structure
1. The PHP script index.php that creates a Web-interface should be placed in the Web-directory of Apache Web-server.
2. Step1_Look_for_books.py, Step2_Create_Table.py, Step3_Update_Table.py.
3. The three step Python scripts scanning for books and generating database (see below).

Requirements
1. LibGen Database http://gen.lib.rus.ec/dbdumps/ or ftp://libgen.org/dbdumps/
2. MySQL, PHP, Apache, Python 2.7 and MySQLdb (Python DB API).

Installation instructions
The easy way is to install MySQL, PHP, Apache with a preconfigured WAMP/LAMP package, for example Vertrigo. Next install Python and MySQLdb.

Configure Apache
Let's assume the book collection is in "E:\BooksNew".

The Apache's httpd.conf has to be modified by adding an alias to the book folder to let Apache access it. Add the following lines to httpd.conf:
Код:
<Directory "E:\BooksNew">
      Options Indexes MultiViews
      AllowOverride None
      Order allow,deny
      Allow from All
</Directory>

Alias /books "E:\BooksNew"
If the server is running, http://localhost/books/ should return the list of books in directory "E:\BooksNew".

Configure MySQL
make sure it employs the standard database port 3306.
make sure that LibGen database is connected.

Configure PHP
change max execution time in php.ini file to two minutes: max_execution_time = 120
this time is sufficient to display 3000 entries, if the bookwarior database is searched this number of results might be returned

Configure the project files
1. Open index.php in text editor and set your password, user name and the book folder:
Код:
$dbuser = 'root';               // your MySQL user name
$dbpass = 'vertrigo';            // your MySQL password
$books_folder = "E:\BooksNew";      // path to your collection of books
2. Open Step1_Look_for_books.py in a text editor and set path to the book folder (do not add extra spaces in Python), for example
Код:
path = ur'E:\BooksNew'

3. the Step2_Create_Table.py and Step3_Update_Table.py files should have correct username and password, for example:
Код:
db = MySQLdb.connect(host="localhost",         # your host
                     user="root",              # your username
                     passwd="vertrigo",        # your password
               ...)
here we have username="root" and password="vertrigo":

Using Python Scripts
The MSQL server should be started first.

The process is split in 3 steps so that each step can be controlled.
Note: Step_1 and Step_3 might require considerable time to complete.
It is desirable to pick folders with few books.

1. Scan the book folder, compute HASHes, then launch Step1_Look_for_books.py. The result will be 2 files in the script directory:
Код:
report.txt            // file with the following structure: HASH   |  File Size  |  Path File Name
report_Err.txt         // error log
2. Now create your SQL database, with would contain information about your books
2.a) Create "mybooks" database in MySQL
From a command line as usual:
Код:
mysql> create database mybooks;

2.b) Launch Step2_Create_Table.py - the script should create table "updated" in the "mybooks" database (the table is a reduced version of updated.bookwarrior)

3) Populate the table "updated.mybooks" and launch Step3_Update_Table.py. If the MD5-hash matches updated.bookwarrior, the information will be transferred to updated.mybooks. If we have the same book occurring multiple times (the file names do not matter), a report "DB_doubles.txt" containing the links to the repeated books will be created.

Finally
Start MySQL and Apache servers and click http://localhost/. In the appeared search field type a keyword, click on the "submit" button. In a moment you should see the list of books in "E:\BooksNew" containing the keyword either in the book Title and/or Author fields. By clicking on the search result, a corresponding file should open.

Instead of the local book database "mybooks", the "bookwarrior" database can be chosen instead (see left-top menue), which contains a significantly larger number of books, which are available on the internet.

Future Plans
Add possibility to describe books from the the web interface.
Add advanced search.
