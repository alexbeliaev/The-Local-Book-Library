path = ur'E:\BooksNew'			# Key for success with rus names, need	"ur", No "\" at the end



# Works with Unicode !!! Tested on Rusian Names !!!
# return the list of all 'pdf','djv','chm','djvu' files in the given directory and below in subdirectories
# get file size and file HASH
# Generates report.txt and report.html of the form:
#   HASH   |  File Size  |  Path to File
# Generate file with Errors if any.




# scan given directory, retern list of files with 'pdf','djv','chm','djvu' filenames
import os
def Scan(path):
    L = []
    for  top, dirs, files in os.walk(path):
        for f in files:
            ext = f[-3:].lower()
            if ext in ('pdf','djv','chm','jvu'):
                link = os.path.join(top, f)
                L.append(link)
                #print link
    return L


#-------------------------------------------------------------------------------
# compute HASH of a file
# reads it block by block (it does not load a file into memory at once)
# "hasher" var can be "hashlib.sha256()" (best) or "hashlib.md5()" (might result in collisions)
# Ex: HASH = hashfile(path, hashlib.md5())

import hashlib
def hashfile(path, hasher, block = 65536):
    f = open(path, "rb")
    buf = f.read(block)
    while len(buf) > 0:
        hasher.update(buf)
        buf = f.read(block)
    # return hasher.digest()
    return hasher.hexdigest()

#-------------------------------------------------------------------------------

L = Scan(path)

L_out = []
L_err = []
for x in L:
    try:
        HASH = hashfile(x, hashlib.md5())
        Size = os.path.getsize(x)
        print Size
        L_out.append((HASH, Size, x))
    except:
        L_err.append(x)


#-------------------------------------------------------------------------------
# Save the results

def GenStr(L):
    s = ""
    for x in L:
        s = s + "%s\t|\t%s\t|\t%s\n" % (x[0],x[1],x[2])             # Use Tabs \t to separate  records!!! Briliant !!!
    return s[:-1]

def GenHTML(L):
    s = "<html><meta http-equiv='content-type' content='text/html; charset=utf-8'><body><table border = 1><tr><td> HASH MD5 <td> File Size <td> File Name"
    for x in L:
        s = s + "<tr><td> %s <td> %s <td> %s " % (x[0],x[1],x[2])
    s = s + "</table></body></html>"
    return s[:-1]


s = GenStr(L_out)
file("report.txt",'wb').write(s.encode('utf8'))

s = GenHTML(L_out)
file("report.html",'wb').write(s.encode('utf8'))

s ="\n".join(L_err)
file("report_Err.txt",'wb').write(s.encode('utf8'))


# To Load Use:
# f = file('report.txt', 'r')
# s =  f.read().decode('utf8')
# L = s.split('\t|\t')
