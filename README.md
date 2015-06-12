# Roukmoute\BenchMemory
Analyze the amount of memory allocated of algorithms or programs by placing some “marks” in the code

### Example

All we have to do is to place different marks in the code.
A mark can be started, stopped and reset.

```php
// Creates and initializes variables.
$i = 0;
$tmp = '';
while ($i < 10000) {
    $tmp .= 'a';
    ++$i;
}
$aHash = array_fill(100000000000000000000000, 100, $tmp);
$benchMemory = new Roukmoute\BenchMemory\BenchMemory();
unset($i, $tmp);

reset($aHash);
// Create a mark called “while”
$benchMemory->while->run();
// The test using while
while (list(, $val) = each($aHash)) ;
$benchMemory->while->stop();

reset($aHash);
// Create a mark called “foreach”
$benchMemory->foreach->run();
// The test using foreach
foreach ($aHash as $val) ;
$benchMemory->foreach->stop();

reset($aHash);
// Create a mark called “for”
$benchMemory->for->run();
// The test using for
$key = array_keys($aHash);
$size = sizeOf($key);
for ($i = 0; $i < $size; $i++) ;
$benchMemory->for->stop();

// Print statistics
echo $benchMemory;

/**
 * Will output:
 * while                296.00 B
 * foreach              48.00 B
 * for                  14.06 KB
 * Peak of memory usage 1.07 MB / 128M
 */
```
