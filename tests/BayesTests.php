<?php
namespace Tests;
use niiknow\Bayes;

class BayesTests extends \PHPUnit\Framework\TestCase
{
    public function test_init_bayes_with_no_options()
    {
        $this->assertTrue(is_object(new Bayes()));
    }

    public function test_correctly_categorize_setiment() {
        $classifier = new Bayes();

        // teach it positive phrases
        $classifier->learn('amazing, awesome movie!! Yeah!!', 'positive');
        $classifier->learn('Sweet, this is incredibly, amazing, perfect, great!!', 'positive');

        // teach it a negative phrase
        $classifier->learn('terrible, shitty thing. Damn. Sucks!!', 'negative');

        // teach it a neutral phrase
        $classifier->learn('I dont really know what to make of this.', 'neutral');

        // now test it to see that it correctly categorizes a new document
        $this->assertTrue($classifier->categorize('awesome, cool, amazing!! Yay.') === 'positive');
    }

    public function test_correctly_categorize_language() {
        $classifier = new Bayes();

        // teach it how to identify the `chinese` category
        $classifier->learn('Chinese Beijing Chinese', 'chinese');
        $classifier->learn('Chinese Chinese Shanghai', 'chinese');
        $classifier->learn('Chinese Macao', 'chinese');

        // teach it how to identify the `japanese` category
        $classifier->learn('Tokyo Japan Chinese', 'japanese');

        // make sure it learned the `chinese` category correctly
        $chineseFrequencyCount = $classifier->wordFrequencyCount['chinese'];

        $this->assertTrue($chineseFrequencyCount['chinese']  === 5);
        $this->assertTrue($chineseFrequencyCount['beijing']  === 1);
        $this->assertTrue($chineseFrequencyCount['shanghai'] === 1);
        $this->assertTrue($chineseFrequencyCount['macao'] === 1);

        // make sure it learned the `japanese` category correctly
        $japaneseFrequencyCount = $classifier->wordFrequencyCount['japanese'];

        $this->assertTrue($japaneseFrequencyCount['tokyo']   === 1);
        $this->assertTrue($japaneseFrequencyCount['japan']   === 1);
        $this->assertTrue($japaneseFrequencyCount['chinese'] === 1);

        // now test it to see that it correctly categorizes a new document
        $this->assertTrue($classifier->categorize('Chinese Chinese Chinese Tokyo Japan') === 'chinese');
    }
}
