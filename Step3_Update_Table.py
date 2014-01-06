# Loads list of books
# search for books description in bookwarrior database, using MD5 HASHes

import MySQLdb

#======================== Connect to database ==================================


con = MySQLdb.connect(host="localhost",
                    user="root",
                    passwd="vertrigo",
                    db="mybooks", charset="utf8", use_unicode=True)
#con.names="utf8"

cursor = con.cursor()             # prepare a cursor object



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


#======================= Load the report with files HASHes =====================
def Load_Report(path):
    f = file(path, 'r')
    s =  f.read().decode('utf8')

    L = s.split('\n')

    L_out = []
    for x in L:
        r = x.split('\t|\t')
        link = r[2]
        link = link.replace('\\', '/')          # deal with / in directory name...
        #print link
        L_out.append((r[0],r[1],link))

    return L_out

#============================== Do Stuff Here ==================================

L = Load_Report('report.txt')


#Delite
#req = "DELETE FROM updated WHERE ID > '0'"        # delite all
#SQL(req)


i = 0
L_OK = []
L_Err = []
L_Err_HASH = []

for x in L:
    HASH = x[0]
    path = x[2]
    DataBase = "bookwarrior.updated"
    col = "Title, Author, VolumeInfo, Year, Edition, Publisher, Pages, Identifier, Language, Extension, Filesize, Library, MD5"
    sql = "INSERT INTO updated (Path, %s) SELECT '%s', %s FROM %s WHERE MD5 = '%s';" % (col, path, col, DataBase, HASH)
    sql = sql.encode('utf8')                    # encode in UTF-8

    i = i+1
    try:
        cursor.execute(sql)                    # execute
        print str(i), 'OK ', path
        L_OK.append(path)
    except:
        L_Err.append(path)
        L_Err_HASH.append(HASH)
        print str(i), "Error ", path




SQL("SELECT Path, Year from updated;")


# Save report
s ="\n".join(L_Err)
file("DB_doubles.txt",'wb').write(s.encode('utf8'))
s ="\n".join(L_Err_HASH)
file("DB_doubles_HASH.txt",'wb').write(s.encode('utf8'))
s ="\n".join(L_OK)
file("DB_OK.txt",'wb').write(s.encode('utf8'))

#============================= The End =========================================
cursor.close ()
con.commit ()
con.close()                                  # disconnect from server

