import cgi
import cgitb
import hashlib
import sqlite3

cgitb.enable()

form = cgi.FieldStorage()

name = form.getvalue("name")
email = form.getvalue("email")
password = form.getvalue("password")
confirm_password = form.getvalue("confirm_password")

# Check if password and confirm_password match
if password != confirm_password:
    print("Content-Type: text/html\n")
    print("<html>")
    print("<head>")
    print("<title>Sign Up Failed</title>")
    print("</head>")
    print("<body>")
    print("<h1>Password and Confirm Password do not match. Please try again.</h1>")
    print("</body>")
    print("</html>")
else:
    # Hash password
    hashed_password = hashlib.sha256(password.encode()).hexdigest()

    # Connect to database
    conn = sqlite3.connect('users.db')
    c = conn.cursor()

    # Check if email already exists in database
    c.execute("SELECT * FROM users WHERE email=?", (email,))
    row = c.fetchone()
    if row is not None:
        print("Content-Type: text/html\n")
        print("<html>")
        print("<head>")
        print("<title>Sign Up Failed</title>")
        print("</head>")
        print("<body>")
        print("<h1>This email already exists. Please use a different email.</h1>")
        print("</body>")
        print("</html>")
    else:
        # Insert new user into database
        c.execute("INSERT INTO users (name, email, password) VALUES (?, ?, ?)", (name, email, hashed_password))
        conn.commit()

        print("Content-Type: text/html\n")
        print("<html>")
        print("<head>")
        print("<title>Sign Up Successful</title>")
        print("</head>")
        print("<body>")
        print("<h1>Sign Up Successful!</h1>")
        print("<p>Your account has been created. Please <a href='index.html'>log in</a>.</p>")
        print("</body>")
        print("</html>")

    # Close database connection
    conn.close()

# Respond with a redirect to address.html
    print("Content-Type: text/html")
    print("Location: address.html")  # Redirect to address.html
    print()  # Print an empty line to indicate the end of headers