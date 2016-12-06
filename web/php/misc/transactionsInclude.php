<?php
/**
 *  Verifies whether the userstamp contains ONLY the characters it should include
 *  
 *  \return TRUE if userstamp is ok. FALSE otherwise.
 */
function verifyUserstamp($stamp) {
	$size = (int)sizeof($stamp);
	for($i = 0; $i < $size; $i++) {
		// If the stamp has invalid characters
		if(!(('A' <= $stamp[$i] && $stamp[$i] <= 'Z') || ('a' <= $stamp[$i] && $stamp[$i] <= 'z') || '0' <= $stamp[$i] && $stamp[$i] <= '9')) {
			return false;
		}
	}
	return true;
}

/**
 *  Creates a random userstamp of the given lenght. The timestamp is build out
 *  of letters of alphabet (minor and major) and numbers.
 *  
 *  \return String
 */
function createUserstamp($length) {
	$sourceStr = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$sourceSize = 62;
	$result = "";
		
	for($i = 0; $i < $length; $i++) {
		$result .= $sourceStr[rand(0, $sourceSize-1)];
	}
	
	return $result;
}

/**
*	Writes the new transaction into a $file
*	Writes the pair $key $value\n
*	Returns the index of the new entry
*	This index is compatible with
*	removeTransaction method.
*/
function addTransaction($filename, $key, $value) {
	// This is totally simple append code
	$file = fopen($filename, 'a');
	fwrite($file, $key.' '.$value.' \n');
	fclose($file);
	
	$out = exec('wc -l '.$filename.' | tr \' \' \'\\n\' | head -1');
	
	return intval($out)-1;
}

/**
*	Finds the transaction specified by $key
*	in $file. Value is saved into $value
*	and index of line is returned.
*	Returns -1 if transaction does not exist.
*/
function findTransaction($filename, $key, &$value) {
	// Read the file into array
	$buffer = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$size = (int)sizeof($buffer);
	
	// Loop lines
	for($i = 0; $i < $size; $i++) {
		// Split lines by spaces
		$line = explode(' ', $buffer[$i]);
		
		// This is a transaction we've searched for
		if ($line[0] == $key) {
			$value = $line[1];
			return $i;
		}
	}
	
	return -1;
}

/**
*	Takes the index $id (which can be obtained)
*	from findTransaction method and removes it
*	from $file.
*/
function removeTransaction($filename, $id) {
	// Read the file into array
	$buffer = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$size = (int)sizeof($buffer);
	
	// Open file for writing
	$file = fopen($filename, 'w');
	
	// Loop $buffer and write it's content't to a file
	for($i = 0; $i < $size; $i++) {
		if($i == $id) continue;	// And skip the to-be removed line
		fwrite($file, $buffer[$i].'\n');
	}
	
	fclose($file);
}
?>
