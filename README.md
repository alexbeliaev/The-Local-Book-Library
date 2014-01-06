The-Local-Book-Library
======================

Assume you have a large book collection in some folder/folders, and you would like to create a database of your books, so that you can know the books description and have an easy way to find the right book, by an author, title, or key word.

The purpose of this project:
1. Create a list of all the books in the given folder.
2. For each book compute MD5 HASH.
3. Using HASHes find book description in LibGen database.
4. Create a MySQL table, containing description of all the books that have been found in the LibGen database.
5. Get access to your collection through the web browser interface and the local HTTP web server.



---------------------------------------------------------------------
[What is needed]
1. LibGen Database http://genofond.org
2. MySQL, PHP, Apache, Python 2.7 
and MySQLdb - is Python DB API http://sourceforge.net/projects/mysql-python/



---------------------------------------------------------------------
[Installing]
The easy way is to install MySQL, PHP, Apache is to install preconfigure WAMP/LAMP package, for example Vertrigo (http://vertrigo.sourceforge.net/) 
Next install Python and MySQLdb (the DB API http://sourceforge.net/projects/mysql-python/ )



---------------------------------------------------------------------
[Configure Apache]
Let's assume the book collection is at the "E:\BooksNew".

The httpd.conf Apache file has to be modified by adding alias on the book folder, so that Apache server would be allowed to access this folder.
Add to "httpd.conf" the following:

<Directory "E:\BooksNew">
		Options Indexes MultiViews
		AllowOverride None
		Order allow,deny
		Allow from All
</Directory>

Alias /books "E:\BooksNew"

Is servers are running by typing in browser 
	localhost/books
the list of books contained in directory "E:\BooksNew" should be displayed.



---------------------------------------------------------------------
[Configure MySQL]
make sure it runs on standard port 3306.
make sure that LibGen database is connected.


---------------------------------------------------------------------
[Configure PHP]
change max execution time in php.ini file to 2 min:

max_execution_time = 120
(this is the sufficient time to display 3000 results, if bookwarior database is search this number of result can occur)



---------------------------------------------------------------------
[Configure the project files]

1. 
index.php
(open in text editor and set your password, the user name and path to the books folder):

$dbuser = 'root';					// your MySQL user name
$dbpass = 'vertrigo';				// your MySQL password
$books_folder = "E:\BooksNew";		// path to your collection of books

2.
Step1_Look_for_books.py
open in text editor and set path to the books folder (do not add extra spaces in Python):

In our example
path = ur'E:\BooksNew'


3.
the Step2_Create_Table.py and Step3_Update_Table.py files should have the correct username and password, for example: 

db = MySQLdb.connect(host="localhost",         # your host
                     user="root",              # your username
                     passwd="vertrigo",        # your password
					...)

here we have username="root" and password="vertrigo":					

					
------------------------------------------------------------------------------------
[Using Python Scripts]

The MSQL server should be started first.

The process is split in 3 steps, so that each step can be controlled. 
It should be noted that Step_1 and Step_3 might require considerable time. 
It would be a good idea to practice at a folder containing only few books. 

1. Scan the book folder, and compute HASHes
Launche Step1_Look_for_books.py

The result would be 2 files in the directory from which the script was launched:
report.txt				// file with the following structure: HASH   |  File Size  |  Path File Name
report_Err.txt			// error log


2. Now create your SQL database, with would contain information about your books
2.a) Create "mybooks" database in MySQL
From a command line as usual: 
mysql> create database mybooks;

2.b) Launch Step2_Create_Table.py 
The script should create Table "updated" in the "mybooks" database (the table is a reduced version of updated.bookwarrier)

3) Populate the table "updated.mybooks"
Launch Step3_Update_Table.py
if a MD5 hash matches updated.bookwarrier, the information would be transferred to updated.mybooks.

If we have the same book occurring multiple times (file names does not mater), the report "DB_doubles.txt" would be created, containing links to these repeated books.


---------------------------------------------------------------------
[Finaly]
Start your PHP, MySQL, Apache servers, and type in browser localhost
In the search field type a keyword, click the "submit" button, and in a moment you should see a list of all the books in "E:\BooksNew" directory, containing this key word in the books title or in the author field.

By clicking on the book the corresponding file should be opened.

Bookwarrior database can be chosen instead (the very top left menu) to search for the books available at the internet.

---------------------------------------------------------------------
[Future Plans]
-> Add possibility to describe books from the the web interface  
-> Add advanced search


The work is in progress, stay tuned :)
alexbeliaev@gmail.com
