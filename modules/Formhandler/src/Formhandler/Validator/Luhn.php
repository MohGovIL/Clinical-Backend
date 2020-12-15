<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 9/4/16
 * Time: 4:38 PM
 */

namespace Formhandler\Validator;
use Laminas\Validator\AbstractValidator;




class Luhn extends AbstractValidator
{
    /*
* This function will check a number (Credit Card, IMEI, etc.) versus the luhn
* algorithm. (Both mod 10 and mod5)
*
* More information on the luhn algorigthm: http://en.wikipedia.org/wiki/Luhn_algorithm
*
* From the Wikipedia entry:
* As an illustration, if the account number is 49927398716, it will be validated as follows:
    1. Double every second digit, from the rightmost: (1×2) = 2, (8×2) = 16, (3×2) = 6, (2×2) = 4, (9×2) = 18
    2. Sum all the individual digits (digits in parentheses are the products from Step 1): 6 + (2) + 7 + (1+6) + 9 + (6) + 7 + (4) + 9 + (1+8) + 4 = 70
    3. Take the sum modulo 10: 70 mod 10 = 0; the account number is valid.
*/
   static function luhn_validate($number, $mod5 = false) {
        $parity = strlen($number) % 2;
        $total = 0;
        // Split each digit into an array
        $digits = str_split($number);
        foreach($digits as $key => $digit) { // Foreach digit
            // for every second digit from the right most, we must multiply * 2
            if (($key % 2) == $parity)
                $digit = ($digit * 2);
            // each digit place is it's own number (11 is really 1 + 1)
            if ($digit >= 10) {
                // split the digits
                $digit_parts = str_split($digit);
                // add them together
                $digit = $digit_parts[0]+$digit_parts[1];
            }
            // add them to the total
            $total += $digit;
        }
        return ($total % ($mod5 ? 5 : 10) == 0 ? true : false); // If the mod 10 or mod 5 value is equal to zero (0), then it is valid
    }
// To Test:

    const ISNUM = 'TEMP';

    protected $messageTemplates = array(
        self::ISNUM => 'ID number is not valid',
    );

    public function __construct(array $options = array())
    {
        parent::__construct($options);
    }
    public function isValid($value)
    {
        return self::luhn_validate($value);
    }
}

?>
