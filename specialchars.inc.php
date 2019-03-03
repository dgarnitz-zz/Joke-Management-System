<?php
function html($text)
{
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function htmlout($text)
{
	echo html($text);
}

function markdown2html($text)
{
	$text = html($text);
	//convert any HTML code present in the text into HTML text
	
	//strong emphasis
	$text = preg_replace('/__(.+?)__/', '<strong>$1</strong>', $text);
	
	$text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
	
	//emphasis
	$text = preg_replace('/_([^_]+)_/', '<em>$1</em>', $text);
	
	$text = preg_replace('/\*([^\*]+)\*/', '<em>$1</em>', $text); 
	
	//Convert windows (\r\n) to Unix (\n)
	$text = preg_replace('/\r\n/', "\n", $text);
	//Conert Mac (\r) to Unix (\n)
	$text = preg_replace('/\r/', "\n", $text); 
	
	//Paragraphs
	$text = '<p>' . str_replace("\n\n", '</p><p>', $text) . '</p>';
	//Line Breaks
	$text = str_replace("\n", '<br>', $text);
	
	// [linked text](link URL)
	$text= preg_replace('/\[([^\]]+)]\(([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)\)/i', '<a href="$2">$1</a>', $text);
	
	return $text; 
}

function markdownout($text)
{
	echo markdown2html($text);
}

?>