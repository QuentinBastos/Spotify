# Spotify-Symfony

# RUN THE WEBPACK : 
    - npm run watch

# RUN THE SERVER :
    - symfony server:start

# RUN THE DATABASE :
```txt
- The database is use in local with wamp or xamp (on phpmyadmin 5.2.1)

```

# RUN THE DATABASE IF PROBLEM WITH WAMP : 
```txt
- database can be run with wamp
- if you have a problem with that like error : 
 ERROR 2059 (HY000): Authentication plugin 'auth_gssapi_client' cannot be loaded: 
 The specified module could not be found.
 
- you can use the command : 
    - net stop MariaDB
    - net start MySQL80

because there is a conflict with both of database
```