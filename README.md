# Usage

```php
<?php
// Guess card type
$guesser = new \Wucdbm\CreditCardGuesser\TypeGuesser();
try {
    $type = $guesser->guess('1234 1234 1234 1234');
} catch (\Wucdbm\CreditCardGuesser\Exception\UnknownCardTypeException $e) {
    // failed to match any of the built-in types
}
// Change Code for a particular card type
$guesser->setCode('Visa', 'SomeOtherCode');
// Change Regex for a particular card type
$guesser->setRegex('Visa', '/anotherRegex1234567/');
// Add another card type
$guesser->addCard('VisaElectron', 'VI', '/someRegex/');
// Remove card type
$guesser->removeCard('Visa');
```

# A word on Regex expressions

Regexes have been collected from the following libraries

- https://github.com/Betalabs/credit-card-type/
- https://github.com/mcred/detect-credit-card-type/

# Contribution

If you need another card type to be included in the standard config, please submit a PR
