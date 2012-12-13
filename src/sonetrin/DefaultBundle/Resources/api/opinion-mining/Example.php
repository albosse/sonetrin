<?php

require_once 'Opinion.php';

class Example
{

    function run()
    {
        $op = new Opinion();
        $op->addToIndex(__Dir__ . '/rt-polaritydata/rt-polarity.neg', 'neg');
        $op->addToIndex(__Dir__ . '/rt-polaritydata/rt-polarity.pos', 'pos');

        $string = "Avatar had a surprisingly decent plot, and genuinely incredible special effects";
        echo "Classifying '$string' - " . $op->classify($string) . "<br />";
        $string = "Twilight was an atrocious movie, filled with stumbling, awful dialogue, and ridiculous story telling.";
        echo "Classifying '$string' - " . $op->classify($string) . "<br />";

        $string = "Party until you pass out, drink until your dead! Dance all night until you cant feel your legs!!! #footballseasonsover #bmth";
        echo "Classifying '$string' - " . $op->classify($string) . "<br />";
        $string = "Ive said it once Ive said it twice, I've said it a thousand fucking times, that I'm ok. That im fine, that this is all just in my mind #bmth";
        echo "Classifying '$string' - " . $op->classify($string) . "<br />";
        $string = "Hotline is now live #msftholiday @microsoft";
        echo "Classifying '$string' - " . $op->classify($string) . "<br />";
    }

    function runFile($sentences)
    {
        $op = new Opinion();
        $op->addToIndex(__Dir__ . '/rt-polaritydata/rt-polarity.neg', 'neg');
        $op->addToIndex(__Dir__ . '/rt-polaritydata/rt-polarity.pos', 'pos');
        
// … snip … article contents as $op setup
//        $sentences = explode(".", $doc);
        $score = array('pos' => 0, 'neg' => 0);
        foreach ($sentences as $sentence)
        {
            if (strlen(trim($sentence)))
            {
                $class = $op->classify($sentence);
                echo "Classifying: \"" . trim($sentence) . "\" as " . $class . "<br />";
                $score[$class]++;
            }
        }
        var_dump($score);
    }

}

?>
