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
require_once __DIR__ . '/Tube.php';
require_once __DIR__ . '/Color.php';
require_once __DIR__ . '/ProgressRecorder.php';
require_once __DIR__ . '/PrintToTerminal.php';
require_once __DIR__ . '/TerminalRow.php';

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

var_dump($board->solve()); //calculates the solution - gives back bool(false) if it's not solvable

$printer = new PrintToTerminal($recorder, 8); //at tubes the line is broken into the next one
echo $printer->print(); //prints the solution
```


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
        foreach ($this->tubes as $tube1) {
            foreach ($this->tubes as $tube2) {
                if ($tube1 !== $tube2) { //prevent putting sth from itself to itself
                    if ($tube2->canReceive($tube1->getExtractable())) {
                        //if there is a possible move
                        //do it and
                        ////spawn new thread  //, to not change this board
                        $newBoard = $this->clone();
                        $result = $newBoard->solvingMove(
                            array_search($tube1, $this->tubes),
                            array_search($tube2, $this->tubes)
                        );
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

        $tube2->doReceive($tube1->getExtractable());
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
        return $this->solve();
    }
```

