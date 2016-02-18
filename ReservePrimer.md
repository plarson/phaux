Hopefully this message will be the first in a series that will evolve
into some sort of cohesive documentation.
REServe is a mechanism to store objects persistently in a relational
database with minimal effort on the part of the programmer. REServe
objects can store basic types (strings,numbers,etc), as well as
complex types like other objects and arrays. REServe allows you to
traverse your object tree much like you would in an object oriented
database while still maintaining a reasonable relational structure.

Any class that you wish to persist in a REServe must subclass REServe.
You must then tell REServe how to store those objects members by
implementing the method tableDefinition(). tableDefinition needs to
return an instance of REServeTableDefinition that should be build from
the objects parent using chained calls to column. An example might
look like ...

```
class Person extends REServe { 
   protected $name; 

...getters and setters go here 

    public function tableDefinition(){ 
        return parent::tableDefinition()-> 
                    column("name",'REString'); 
    } 
... 

} 
```

As you can see tableDefinition calls parent::tableDefinition() and
adds one column to it telling reserve that it is a REString. REString
is a basic type. A basic type is a member var that can be stored along
side the object in the table.
The basic types are
> REBoolean
> REDate
> REDateAndTime
> REFloat
> REInteger
> REString
> RETime

REServe is expandable and basic types can be added without much
difficultly but that is beyond the scope of this primer.

REServe gets and sets the member vars of the object by using key
paths. Key paths are getters and setters that conform to the keyPath
naming convention. In the above example the key path defined is name.
The getters and setters would be defined as follows ...

```
public function name(){ 
    return $this->name; 

} 

public function setName($aString){ 
    $this->name = $aString; 
    return $this; 
} 
```

If you would like REServe to access your objects member vars directly,
even if they are declared protected/private your object should
subclass REServeDirectMemberVarAccess.
Complex types are types who's data can not be stored in the table
along side the basic types. Complex types include arrays and other
subclasses of REServe. Lets say that we wanted our above example to
also store an array of strings. Lets say this array contains a list of
phone numbers (stored as string) for our person. You would define
tableDefinition() as follows ...

```
     public function tableDefinition(){ 
        return parent::tableDefinition()-> 
                    column("name",'REString')-> 
                    column('phoneNumbers',REArray::of('REString')); 
    } 
```

You must also define the getters and setter on the key path
'phoneNumbers' as described above. REServe can store anything in an
array that REServe can normaly persist in an object including other
arrays.

Storing objects is just as simple as storing basic types.

```
   column('someMember','REServeClassName') 
```

Where REServeClassName is the name of your subclass of reserve that
you want to persist.

REServe also supports polymorphism on a member var that stores an
object. By declaring your type as REServeAnySubclass any subclass of
REServe can be stored at that key path. For example you might have
more than one user class depending on where your user is originating
from.

```
     public function tableDefinition(){ 
        return parent::tableDefinition()-> 
                    column("name",'REString')-> 
                    column('loginUser','REServeAnySubclass'); 
    } 
```

By declaring loginUser as a REServeAnySubclass REServe will use a
lookup table to determine the class of the object and where it is
stored. This does come with a performance penalty as two queries are
preformed instead of one. Special care must be use when declaring a
REArray::of('REServeAnySubclass'). REArray is optimized to bring in
all objects in one query but this can not be done if the REArray is of
REServeAnySubclass so two queries must be performed for every object
in the array.

To connect to a REServe call connect on an instance of the appropriate
reserve driver that matches your relational datastore. Currently only
MySQL is supported but Postegress is planed (the Smalltalk version has
postgress support).

```
$db = Object::construct('REServeMySQLDriver'); 
$db->connect($host,$user,$password,$database); 
```

Table creation can either be done explicitly or implicitly. If you
would like to let reserve create the tables as needed set automatic
table creation to true ...

```
$db->setAutomaticTableCreation(true); 
```

The first time you use a REServe database it must be setup before you
can store anything to it.

```
$db->setupDatabase(); 
```

If you want to explicitly create the tables for an object call
addClass on your REServe datastore instance passing it a string that
is the name of the class you want to add.

```
$db->addClass('ASubclassOfReserve'); 
```

Storing REServeable objects in a REServe is easy and is usually
totally transparent. REServe uses persistence by reachability. That
means that if a REServable object is referenced by an object in a
REServe database it will be persisted on the next commit. If you would
like to store an object manually call the reServeIn() method on that
object passing it an instance of you reserve driver.

```
$reservable->reServeIn($db); 
```

REServe has a special object call the root. This root object can be
accessed by calling root on your instance of the reserve driver. And
can be set by calling setRoot passing an instance of a reservable
object

```
$db->setRoot($aReserveable); 
/* 
**Now root() will always return $aReserveable 
*/ 
$aReserveable = $db->root(); 
```

All of your REStore operations should be done inside a transactions.
To start a transaction call startTransaction and to commit the changes
call commit.

```
$db->startTransaction(); 
...Change some data... 
$db->commit(); 
```

You can also call commitAndStart() starting a new transaction after commit.

If you want to discard all changes that you made to your object model
since you last commit call rollback().

REServe stores objects in a cache. It is likely that you will want to
remove old objects from your cache periodically. Calling flush() will
remove all objects from your REServe cache. Any object that is access
will be brought back in from the database.

If you are using Phaux with REServe most of the dirty work of making
connections, committing objects, flushing the cache is handled by
WHREServeSession. If you want to use REServe with your Phaux
application it is recommend that you configure your application to use
WHREServeSession or a subclass.

RESeve is probably the most complex set of classes include in the core
Phaux codebase. The above is by no means exhaustive (I have not
covered querying, optimizing, indexes, ...) but I hope it will be
enough to get you started. For a more complete example of a
REServeable class check of WHREServeContactModel included in the Phaux
source under REServe-Phaux.
