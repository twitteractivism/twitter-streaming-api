<?php
/*
* File for registering error log to `error_log_folder` folder
*/
error_reporting(0);  // to create  a own error handling

// user defined function

function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars)
{
// timestamp for the error entry.
$dt = date('Y-m-d H:i:s (T)');

// errors types

$errortype = array (
E_ERROR => 'Error',
E_WARNING => 'Warning',
E_PARSE => 'Parsing Error',
E_NOTICE => 'Notice',
E_CORE_ERROR => 'Core Error',
E_CORE_WARNING => 'Core Warning',
E_COMPILE_ERROR => 'Compile Error',
E_COMPILE_WARNING => 'Compile Warning',
E_USER_ERROR => 'User Error',
E_USER_WARNING => 'User Warning',
E_USER_NOTICE => 'User Notice',
E_STRICT => 'Runtime Notice'
);

$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);

$err = "<errorentry>\n";
$err .= "\t<datetime>" .$dt. "</datetime>\n";
$err .= "\t<errornum>" .$errno. "</errornum>\n";
$err .= "\t<errortype>" .$errortype[$errno]. "</errortype>\n";
$err .= "\t<errormsg>" .$errmsg. "</errormsg>\n";
$err .= "\t<scriptname>" .$filename. "</scriptname>\n";
$err .= "\t<scriptlinenum>" .$linenum. "</scriptlinenum>\n";

if (in_array($errno, $user_errors)) {
$err .= "\t<vartrace>" .wddx_serialize_value($vars, 'Variables'). "</vartrace>\n";
}
$err .= "</errorentry>\n\n";

// write the errors to one file that is

$filename=$_SERVER["DOCUMENT_ROOT"].'/error_log_folder/error_log.log' . date('Y-m');
error_log($err, 3, ''.$filename); //add absolute path to public_html in between single quotes

}

// to call the user defined function

$old_error_handler = set_error_handler('userErrorHandler');

?>