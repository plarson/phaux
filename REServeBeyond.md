In REServe Primer I covered the basics of using REServe. There are
times when follow by reference from the root object will not be the
best way of dealing with your data and you may want to query your data
in a relational manner. One of the strengths of REServe is that it
allows you to deal with your data in both an object oriented manner
and a relational one. Queries can be performed in one of two ways and
I will cover them both.

The method queryFor($aClass) of your REServeDriver class returns an
instance to REQuery that can be used to query for object of the same
class that meet your specified criteria. For example if you are using
WHREServeSession as your session class to perform a query on the class
Person you might ...

```
    $this->session()->db()->queryFor('Person')-> 
                where()->keyNameStartsWith('firstName','W')-> 
                andWhere()->keyNameIs('lastName','Harford')-> 
                results(); 
```

This would return an array of Person objects stored in the reserve
who's first name starts with W and last name is Harford. REQuery is
rather primitive now. It does not do more than the above but in a
number of cases that is all you need. I do plan on expanding more on
REQuery in the future but it has worked for me in a number of tasks
now and I have not yet needed the complexity.

REQuery does not yet handle joining tables so you can not of yet use
the values of an object stored in another object as criteria.

You can query your data with regular SQL. By sending an SQL query to
your REServeDriver instance's executeQueryFetchArray method REServe
will perform the query and return you the data as an multidimensional
array.

```
    $this->session()->db()->queryFor($someResult[$currentRow]['objectId']); 
```

> If you want to modify the data it is advised that you fetch the
object out of the REServe by using
objectForOidWithClass($objectId,$className). This will bring the
object into reserve and allow you to treat it as a first level object.
Any changes you make to the object will be committed on the next call
to commit().

A typical column definition is reserve might look like ...

```
public function tableDefinition(){ 
    return parent::tableDefinition()-> 
                 column('userName','REString')-> 
                 column('lastName','REString')-> 
                 column('firstName','REString')-> 
                 column('email','REString')-> 
                column('password','REString'); 

} 
```

But column can take 3 more arguments. The first two arguments to
column are keyPath and type. The third argument is the name of the
column in the database. By default REServe names it the same as the
key path but it could be anything you like. The fourth argument tells
reserve that it should update the value if the object has changed and
is used for the objectId. The last argument is if the field should be
indexed. If the the 5th argument is true reserve will index the field
for you.
It is important to note REServe is not tooled to map to an existing
database. Because of the way REServe deals with ObjectId's and the
assumptions it makes it would not function properly on most
non-REServe databases.

Most table migration happens automatically in REServe. But in the case
of changing a column type or removing a column manual intervention
will be needed.