<?php
include_once '../assets/includes/main.php';
include_once '../../blog_system/assets/settings/blog_settings.php';
//check_loggedin($pdo, '../accounts_system/index.php');
//should make sure user is admin, editor, developer here
if (!function_exists('short_text'))
{
    function short_text($text, $length)
    {
        $maxTextLength = $length;
        $aspace = " ";
        if (strlen($text) > $maxTextLength)
        {
            $text = substr(trim($text), 0, $maxTextLength);
            $text = substr($text, 0, strlen($text) - strpos(strrev($text), $aspace));
            $text = $text . '...';
        }
        return $text;
    }
}

if (!function_exists('byte_convert'))
{
    function byte_convert($size)
    {
        if ($size < 1024)
            return $size . ' Byte';
        if ($size < 1048576)
            return sprintf("%4.2f KB", $size / 1024);
        if ($size < 1073741824)
            return sprintf("%4.2f MB", $size / 1048576);
        if ($size < 1099511627776)
            return sprintf("%4.2f GB", $size / 1073741824);
        else
            return sprintf("%4.2f TB", $size / 1099511627776);
    }
}//end if function exists byte_convert
if (!function_exists('post_author'))
{
    /**
     * Retrieves the author's username based on their ID.
     *
     * @param int $author_id The ID of the author.
     * @return string The author's username or '-' if not found.
     */
    function post_author($author_id)
    {
        // Use global $pdo connection
        global $pdo;

        $author = '-';

        $stmt = $pdo->prepare("SELECT username FROM accounts WHERE id = ? LIMIT 1");
        $stmt->execute([$author_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row)
        {
            $author = $row['username'];
        }

        return $author;
    } //end of function post_author
}// end of function post_author exists
if (!function_exists('generateSeoURL'))
{
    /**
     * Generates a SEO-friendly URL from a given string.
     *
     * @param string $string The input string to convert.
     * @param int $random_numbers Whether to append random numbers (1) or not (0).
     * @param int $wordLimit The maximum number of words to include in the URL.
     * @return string The generated SEO-friendly URL.
     */

    function generateSeoURL($string, $random_numbers = 1, $wordLimit = 8)
    {
        $separator = '-';

        if ($wordLimit != 0)
        {
            $wordArr = explode(' ', $string);
            $string = implode(' ', array_slice($wordArr, 0, $wordLimit));
        }

        $quoteSeparator = preg_quote($separator, '#');

        $trans = array(
            '&.+?;' => '',
            '[^\w\d _-]' => '',
            '\s+' => $separator,
            '(' . $quoteSeparator . ')+' => $separator
        );

        $string = strip_tags($string);
        foreach ($trans as $key => $val)
        {
            $string = preg_replace('#' . $key . '#iu', $val, $string);
        }

        $string = strtolower($string);
        if ($random_numbers == 1)
        {
            $string = $string . '-' . rand(10000, 99999);
        }

        return trim(trim($string, $separator));
    }// end of function generateSeoURL
}  //end of function exists generateSeoURL