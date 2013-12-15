<?

print "Blah!\n";

require_once 'include/gentools.inc.php';

print "Blah!\n";

list($headers, $file) = MessageParser::read("sample.wiki");

print "Blah!\n";

print_r($headers);

$headers['Author'] = "The Grand Canyon";

MessageParser::write("test.wiki", $headers, $file);

?>
