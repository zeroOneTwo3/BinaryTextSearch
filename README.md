# BinaryTextSearch
Binary search into sorted formatted file with key-value pairs (where the key is unique and specified):
>key1 \t value1 \x0А

>key2 \t value2 \x0А

>...

>keyN \t valueN \x0А

Where:

\x0A ~ ASCII=0Ah or LF

\t ~ ASCII=09h
  
## Theory  
  To implement a binary search, you start repeatedly narrow the focus of where you're looking for something. 
  You start in middle. If the thing you're looking for comes before the middle, you look between the beginning and the middle. 
  If it comes after the middle, you look between the middle and the end. When you recurse or iterate, the definition of either "end" or 
  "start" changes to be what was the middle - thus narrowing the scope. This isn't quite O(log N), it's actually O(2*log N) - it takes 
  approximately twice as many searches, depending on the length of strings being lookedup, because we aren't looking up exact record 
  boundaries, only byte boundaries. (https://www.perlmonks.org/?node_id=242942)
