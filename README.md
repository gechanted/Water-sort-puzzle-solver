This Program brute-solves these games  
https://play.google.com/store/search?q=water%20sort%20puzzle&c=apps&gl=DE  
This is the game I have on my phone  
https://play.google.com/store/apps/details?id=com.gma.water.sort.puzzle&gl=DE

The Game works like this:   
You have Tubes (Containers) with fluids (blocks) stored in them  
as long as they don't overflow the Tube (Container)  
The height is usually 4 (blocks high)  
This program can solve various heights  
A tube (Container) is considered done if:   
 **- all fluids (blocks) are the same color**  
 **- and if the tube (Container) can no longer take in fluids (blocks)**  
 **- or tube (Container) is empty**  
The game is solved if every Tube is done

*I didn't exactly follow coding standards coding this project.  
Please don't think I always code this way,  
it was just more (code-)efficient to code it this way*

how to set up:  
*read (and change or copy) the test.php for an example*
```php
require_once __DIR__ . '/Board.php';
require_once __DIR__ . '/BoardFactory.php';
require_once __DIR__ . '/Tube.php';
require_once __DIR__ . '/HashLog.php';
require_once __DIR__ . '/Color.php';
require_once __DIR__ . '/ProgressRecorder.php';
require_once __DIR__ . '/PrintToTerminal.php';
require_once __DIR__ . '/TerminalRow.php';
require_once __DIR__ . '/Timer.php';

$recorder = new ProgressRecorder(); //records the progress of solution

$tube1 = new Tube(1);
$tube1->addColor(new Color('blue'));
$tube1->addColor(new Color('blue'));
$tube1->addColor(new Color('pink'));
$tube1->addColor(new Color('pink'));
// ... and some more tubes 

$board = new Board([ //contains the tubes and solves the problem
    $tube1, $tube2, $tube3, $tube4, $tube5, $tube6, $tube7, $tube8, $tube9, $tube10, $tube11, $tube12, $tube13
], $recorder);// is passed and filled by reference- I know that could be better

//I also have a BoardFactory, which streamlines this process
//see test2.php for further information 

var_dump($board->solve($recorder)); //calculates the solution - gives back bool(false) if it's not solvable

$printer = new PrintToTerminal(8); //at 8 tubes the line is broken into the next one
echo $printer->print(); //prints the solution
```


example output:
````
bool(true)


_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-

 > Step 0          
       1                                                
 |   pink   |        2             3                    
 |   blue   |  |   blue   |  |          |        4      
 |   blue   |  |   blue   |  |          |  |   pink   | 
 |   blue   |  |   pink   |  |          |  |   blue   | 
 +----------+  +----------+  +----------+  +----------+ 
--------------------------------------------------------

 > Step 1          1 -> pink -> 3
       1                                                
 |          |        2             3                    
 |   blue   |  |   blue   |  |          |        4      
 |   blue   |  |   blue   |  |          |  |   pink   | 
 |   blue   |  |   pink   |  |   pink   |  |   blue   | 
 +----------+  +----------+  +----------+  +----------+ 
--------------------------------------------------------

 > Step 2          4 -> pink -> 3
       1                                                
 |          |        2             3                    
 |   blue   |  |   blue   |  |          |        4      
 |   blue   |  |   blue   |  |   pink   |  |          | 
 |   blue   |  |   pink   |  |   pink   |  |   blue   | 
 +----------+  +----------+  +----------+  +----------+ 
--------------------------------------------------------

 > Step 3          4 -> blue -> 1
       1                                                
 |   blue   |        2             3                    
 |   blue   |  |   blue   |  |          |        4      
 |   blue   |  |   blue   |  |   pink   |  |          | 
 |   blue   |  |   pink   |  |   pink   |  |          | 
 +----------+  +----------+  +----------+  +----------+ 
--------------------------------------------------------

 > Step 4          2 -> blue -> 4
       1                                                
 |   blue   |        2             3                    
 |   blue   |  |          |  |          |        4      
 |   blue   |  |          |  |   pink   |  |   blue   | 
 |   blue   |  |   pink   |  |   pink   |  |   blue   | 
 +----------+  +----------+  +----------+  +----------+ 
--------------------------------------------------------

 > Step 5          2 -> pink -> 3
       1                                                
 |   blue   |        2             3                    
 |   blue   |  |          |  |   pink   |        4      
 |   blue   |  |          |  |   pink   |  |   blue   | 
 |   blue   |  |          |  |   pink   |  |   blue   | 
 +----------+  +----------+  +----------+  +----------+ 
_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-
Process finished with exit code 0

````


*from Board.php*  
The exact solving process:
```php
    public function solve(): bool
    {
        //detect completion
        $isSolved = $this->isSolved();

        if ($isSolved) {
            $this->recorder->recordBoard($this);
            return true;
        }
        
        //calculate the possible moves 
        foreach ($this->tubes as $k1 => $tube1) {
            foreach ($this->tubes as $k2 => $tube2) {
                if ($tube1 !== $tube2) { //prevent putting sth from itself to itself
                    if ($tube2->canReceive($tube1->getExtractable())) {
                        //if there is a possible move: do it
                        //in a new thread, to not change this board
                        $newBoard = $this->clone();
                        $result = $newBoard->solvingMove($k1, $k2);
                        //if the solution is correct 
                        if ($result) {
                            //log this part in the solving process
                            $this->recorder->recordBoard($this);
                            //and pass on the good news
                            return true;
                        }

                    }
                }
            }
        }
        return false;
    }

    public function solvingMove($tube1Index, $tube2Index): bool
    {
        $tube1 = $this->tubes[$tube1Index];
        $tube2 = $this->tubes[$tube2Index];

        $extract = $tube1->getExtractable();
        $tube2->doReceive($extract);
        $tube1->doExtract();

        //make a short identifier for this board constellation
        //to ensure no infinite loops (and better calculation times)
        $hash = $this->hash();
        if (array_search($hash, self::$generalLog)) {
            return false;
        }
        self::$generalLog[] = $hash;

        //this board was cloned and has now changed its content
        //start solving further (recursion call)
        $result = $this->solve();
        //if the solution is correct 
        if ($result) {
            //log this part in the solving process
            //I know here shouldn't be any text generation, but it's for the time being easier this way
            $this->recorder->recordMove($tube1->getNr() . ' -> ' . $extract[0]->getColorName() . ' -> ' . $tube2->getNr());
            return true;
        }
        return false;
    }
```

Since I had some spare time I rebuild the actual game in commandline: 
see startGame.php as an example
```php
$boardFactory = new BoardFactory();
$boardFactory
    ->addTube(['red', 'yellow', 'lightgreen', 'darkgreen'])
    ->addTube(['red', 'yellow', 'lightgreen', 'darkgreen'])
    ->addTube(['red', 'yellow', 'lightgreen', 'darkgreen'])
    ->addTube(['red', 'yellow', 'lightgreen', 'darkgreen'])
    ->addTube([])
    ->addTube([]);

$board = $boardFactory->createBoard();
$game = new Game($board, new PrintToTerminal(6,10));
$game->main();
```