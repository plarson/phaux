# Key Paths #

A key path is a standard way Phaux and REServe access object properties.

Lets say you have an object property named foobar. Any object that conforms to the Phaux key path protocol would try and get that value by calling the foobar() method on that properties object and set the value using setFoobar($aValue).

You might define your class as follows
class Barstool extends Object {
    protected $foobar;
    
    public function foobar(){
        return $this->foobar;
    }
   
    public function setFoobar($aValue){
        $this->foobar = $aValue;
        return $this; //Returning this is good practice 
                            // for any function that does not logically
                            // return anything and it allows you to 
                            // chain method calls
    }
} 
 ```