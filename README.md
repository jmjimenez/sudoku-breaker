# PHP Sudoku Breaker
### Author: Jose Manuel Jimenez Garcia
### Email: jmjimenezg@gmail.com

##Description
This project is a sudoku solver implemented in php 7.4

It will not use brute force to solve them. Instead, it will implement some algortithms (strategies), and it will apply them
to the sudoku until it is solved, or the app finds it is out of options (see parameter `--max_iterations` below).

At this current stage is a console app. So you can use the integrated docker commands
or run the app directly using your php interpreter if you have it installed.

You can read a bit more in [my personal blog](http://jmjg.es)

##Requirements
- Docker
- make

## How to run it using docker
- Initialize app:
```make install```
- Run phpunit:
```make unit-test```
- Run app using a sample file `test.json` saved in folder `data\samples`:
```make run file=test.json```

## How to run it using your own php interpreter
- Run phpunit:
```php bin\phpunit```
- Run app using a sample file `test.json` saved in folder `data\samples`:
```bin\console sudoku:solve ./data/samples/test.json```

This console command have several additional options available:
 * `--max_iterations=NNN` will limit the number of iterations to do before leaving the sudoku for unsolved
 * `--return_steps` will return the list of performed steps with the response
 
 ## Sudoku data files
 They are simple json. They must have the following format:
```
   {
     "board": {
       "fixedCells": [
         { "row": 1, "column": 1, "value": 1 },
         { "row": 1, "column": 2, "value": 2 }
       ]
     }
   }
```
There are some samples in folder `data/samples`

## Folder structure
```
---- config: symfony configuration
  |- data: data samples
  |- public: reserved for API index.php
  |- scr 
  |  |- Domain: all classes related to the problem domain
  |  |  |- Sudoku: just in case there might be in the future more subdomains involved
  |  |     |- Application: one class for use case
  |  |     |- Entity: all classes responsible for keeping the status
  |  |     |- Exception: all exeptions in the domain
  |  |     |- Strategy: all classes responsible for solving the sudoku
  |  |        |- Strategy
  |  |           |- Substrategy: auxiliary classes to help the main solving classes 
  |  |     |- ValueObject: all needed value objects
  |  |        |- CellContent: all possible content of a sudoku cell
  |  |        |- Event: all posible events that may happen in the application
  |  |- Infrastructue: all classes needed for implement the application but not related with the problem domain
  |     |- Command: console command entry point
  |     |- Controller: reserved for API controllers 
  |     |- Domain: classes needed to implement specific feature of the domain but outside of the domain
  |     |- Exception: all exceptions in the infrastructure
  |     |- Helper: auxiliary classes needed for the rest of the infrastructure
  |- tests
     |- scripts: uncommitted folder for keeping quick and dirty scripts
     |- Unit: php unit folder

```




