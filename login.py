#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Created on Sat Apr 29 21:42:38 2023

@author: tori
"""

#!/usr/bin/env python

import cgi
import cgitb

cgitb.enable()

form = cgi.FieldStorage()

username = form.getvalue("username")
password = form.getvalue("password")

if username == "myusername" and password == "mypassword":
    print("Content-Type: text/html\n")
    print("<html>")
    print("<head>")
    print("<title>Login Successful</title>")
    print("</head>")
    print("<body>")
    print("<h1>Login Successful!</h1>")
    print("</body>")
    print("</html>")
else:
    print("Content-Type: text/html\n")
    print("<html>")
    print("<head>")
    print("<title>Login Failed</title>")
    print("</head>")
    print("<body>")
    print("<h1>Login Failed. Please try again.</h1>")
    print("</body>")
    print("</html>")
