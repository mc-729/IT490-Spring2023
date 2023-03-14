import smtplib, ssl

sender_email = "cocktailsearch@gmail.com"
receiver_email = "sl236@njit.edu"
message = """\
Subject: Hi there

This message is sent from Cocktail Search."""

port = 465  # For SSL
smtp_server = "smtp.gmail.com"
password = "uscfqvhdhxqwrmxg"

# Create a secure SSL context
context = ssl.create_default_context()
with smtplib.SMTP_SSL(smtp_server, port, context=context) as server:
    server.login(sender_email, password)
    server.sendmail(sender_email, receiver_email, message)


