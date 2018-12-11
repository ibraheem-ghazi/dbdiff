# DBDiff (Database Differenece Compare)


DBDiff is a php based script that helps developers to find the difference between two databases structure,catch new added columns, deleted columns, changed columns and which attributes are changed, and so on, with queries generate like (ALTER,CREATE ...etc)

# Screenshots
![dbdiff queries](https://raw.githubusercontent.com/ibraheem-ghazi/dbdiff/master/screenshots/dbdiff-queries.png)
![dbdiff results](https://raw.githubusercontent.com/ibraheem-ghazi/dbdiff/master/screenshots/dbdiff-results.png)


# Features
  - show difference each table as block
  - show only differences
  - generate queries for separately for each column or table
  - can merge queries and copy it all at once.
  - show column attribute old value and new value.
  - view actual source for old or new by adding source=1 or source=2
  - *MYSQL Dependent* WITHOUT using and system command like `mysqldump`
  - simple ui


# Why DBDiff ?
after long search for scripts to use all solution was executing `mysqldump` system command using `exec` or `shell_exec` in php and get the output as sql dump then compare the difference. which is not accurate and can have many wrong information based on text position , also not clear what excatly change is happened. that's why i built this library which is based on executing mysql `show` and `desc` tables and parsing information manually then print the analysed results alongside with it's queries.

### TODOs
- [X] Handle Create Tables
- [X] Handle Alter Tables
- [ ] Handle Drop table Queries
- [ ] Handle constraints (PK,FK,index, ... etc)
- [ ] Handle events
- [ ] Handle functions
- [ ] Handle procedures
- [ ] Handle triggers
- [ ] Handle views

### Installation

to install this library using composer:
```sh
composer require ibraheem-ghazi/dbdiff
```

> *NOTE:* Secure the script and don't allow public access to it.

### Development

Want to contribute? Great!
i accept pull requests.

License
----

MIT
