# realm

## Core process

Requests will be redirect to `index.php` by Apache2 Rewrite Module and the path tree will be converted in query string splitted as:

- domain
- subdomain
- request

Include Files, as images and scripts, that will be ignored in that rule:

- scripts: `.js`
- stylesheets: `css`, `less`
- images: `png`


## Configuration
Configuration files can be stored on folder _**settings/**_ in `json` format.


## Database

```php
# Open a connection with a database
$statement = new \Realm\DatabaseStatement('database_name');

# Prepare a query
$statement->Prepare('' .
    'SELECT ' .
        '`a`.`field` AS `result` ' .
    'FROM ' .
        '`:table`' .
    'WHERE ' .
        '`a`.`numeric` = :number AND ' .
        '`a`.`text`    = :string ' .
';');

# Adjust the query with non field value
$statement->BindConstant('table', 'my_table');

# Bind values on the statement
$statement->BindValue('number', 1,      \PDO::PARAM_INT);
$statement->BindValue('string', 'test', \PDO::PARAM_STR);

# Bind a variable to receive a return value on a statement
$statement->BindResult('resuly', $result, \PDO::PARAM_STR);

# Execute the query
$result = $statement->Execute();
if (!$result) {
    echo $statement->ErrorInfo();
}

# Run through rows
while ($result && $statement->Fetch()) {
    echo "Result: {$result}.\n";  
}
```


## Domains

The _Domains_ is the area to develop the enviroment. The folder structure can have 3 levels: domain, subdomain and request. Domain and Subdomain levels can be a folder used to contain the lower levels.

When a domain, subdomain, or request is called, a file `.php` and a file .`html` will be searched to be used. The file `.php`, if it exists, will be executed before and the file .`html` after, if it exists.

If the call is invalid, a ***default*** file will be search to be used instead show a *Not Found* return.

A `$this->realm->redirect` variable can be set on the `.php` file to redirect a call to another domain/subdomain/request.

The Domain may have a ***class*** folder where classes can be created and autoloaded on demand. The class `nameserver` must be the Domain name.

Structure Sample:

- ***domains/***
    - domain01/
        - ***classes***/
            - class01.php
            - class02.php
        - default.html
        - default.php
        - subdomain01/
            - default.php
            - request01.html
            - request02.html
            - request02.php
            - request03.html
    - domain02/
        - subdomain02.php
        - subdomain02.php
    - domain03.php


### Add a git submodule as Domains

Use this command to add a git repository as submodule to Domains:

    git submodule add <repository path> domains
