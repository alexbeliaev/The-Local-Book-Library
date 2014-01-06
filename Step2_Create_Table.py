# Create table updated

import MySQLdb


#======================== Connect to database ==================================

# Create an Object = connection to database
db = MySQLdb.connect(host="localhost",          # your host, usually localhost
                     user="root",               # your username
                      passwd="vertrigo",        # your password
                      db="mybooks")             # name of the data base

# prepare a cursor object using cursor() method
cursor = db.cursor()

#========================== Use sql from Py ====================================

# Show the SQL response
def SQL(sql):
    s = ""
    try:
        cursor.execute(sql)                    # execute
        for x in cursor.fetchall():
            print x
            s = s + str(x) + "\n"
    except:
        print " !!!!!!!! Error happened !!!!!!!!!! "
    return s


#====================== Create table as per requirement ========================

SQL("DROP TABLE IF EXISTS updated;")           # Carefull !!! Drops the Table!!.


sql = """CREATE TABLE updated (
  ID INT(15) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  Title VARCHAR(1000) NOT NULL DEFAULT '',
  Author VARCHAR(300) NOT NULL DEFAULT '',
  VolumeInfo VARCHAR(100) NOT NULL DEFAULT '',
  Year INT(4) UNSIGNED DEFAULT NULL,
  Edition VARCHAR(50) NOT NULL DEFAULT '',
  Publisher VARCHAR(100) NOT NULL DEFAULT '',
  Pages INT(10) UNSIGNED DEFAULT NULL,
  Identifier VARCHAR(100) NOT NULL DEFAULT '',
  Language VARCHAR(50) NOT NULL DEFAULT '',
  Extension VARCHAR(50) NOT NULL DEFAULT '',
  Filesize BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
  Library VARCHAR(50) NOT NULL DEFAULT '',
  MD5 CHAR(32) NOT NULL UNIQUE KEY,
  Topic VARCHAR(500) DEFAULT '',
  Commentary VARCHAR(10000) DEFAULT '',
  Path VARCHAR(733) DEFAULT ''
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8"""


SQL(sql)


#======================== disconnect from server ===============================
cursor.close ()
db.close()                                  # disconnect from server
