# CalculatorSF5
## A test for a job interview asked to be made public (Symfony 5 / PHP 8)

The instructions : 
 * Only + - * / are asked (i added support for decimal and negative numbers because i didn't know if i had to)
 * Visuals are to be made with JS CSS and Twig or React (I used Twig / JS / CSS, i cheated a little with Bootstrap)
 * The calculator should take the operator priorities (* / before + -)
 * The calculation has to be made by a Symfony Service
 * Can't use eval
 
 Algorithm wise it's basically a watered down Shunting yard with a watered down postfix calculator to parse the infix of the SY.
 The Js part should be user friendly enough to not be a pain to use (i focused on the php side) and will display results or errors in a div below the calc.
 
 `docker-compose up`
Should bring enough for you to test locally, it uses the port 80 so check if you haven't got anything on it.
