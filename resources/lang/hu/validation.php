<?php
/**
 * ----------------------------------------------------------
 * Create all of the validation error messages
 * ----------------------------------------------------------
 */
return [
    'alpha' => 'A(z) ":field" mező nem tartalmazhat számokat, ékezetes karaktereket és speciális karaktereket',
    'alphaNumeric' => 'A(z) ":field" mező nem tartalmazhat ékezetes karaktereket és speciális karaktereket',
    'between' => 'A(z) ":field" mező csak :min és :max közötti számot tartalmazhat',
    'callback' => 'A(z) ":field" mező érvénytelen',
    'date' => 'A(z) ":field" mező dátum formátuma nem megfelelő',
    'email' => 'A(z) ":field" mező csak érvényes e-mail cím lehet',
    'equals' => 'A(z) ":field" mező nem egyezik a várt értékkel',
    'equalsField' => 'A(z) ":field" mező nem egyezik meg a(z) ":other" mező értékével',
    'in' => 'A(z) ":field" mező érvénytelen értéket tartalmaz',
    'integer' => 'A(z) ":field" mező csak egész számot tartalmazhat',
    'ipAddress' => 'A(z) ":field" mező csak érvényes IP cím lehet',
    'max' => 'A(z) ":field" mező nem tartalmazhat nagyon szamot, mint :max',
    'min' => 'A(z) ":field" mező nem tartalmazhat nagyon szamot, mint :min',
    'notIn' => 'A(z) ":field" mező érvénytelen értéket tartalmaz',
    'numeric' => 'A(z) ":field" mező csak számot tartalmazhat',
    'regex' => 'A(z) ":field" mező érvénytelen',
    'required' => 'A(z) ":field" mező megadása kötelező',
    //
    'oneOfTwo' => 'A(z) ":field" mező és ":other" mezők közül pontosan egy kell, hogy megadva legyen',
    'layout' => 'Az ":field" mezőnek tartalmaznia kell legalább egy blokkot, kivéve ha üres',
];
