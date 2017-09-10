<?php

namespace niiknow;

/**
 * NaiveBayes classifier
 * ported from nodejs: https://github.com/ttezel/bayes
 */
class Bayes
{
    public $categories;
    public $docCount;
    public $totalDocuments;
    public $vocabulary;
    public $vocabularySize;
    public $wordCount;
    public $wordFrequencyCount;
    public $stopWords;

    public $ser = [
        'categories', 'docCount',
        'totalDocuments',
        'vocabulary', 'vocabularySize',
        'wordCount', 'wordFrequencyCount',
        'stopWords'];

    protected $tokenizer;
    protected $options;

    /**
     * initialize an instance of Bayes
     * @param array $options
     */
    public function __construct($options = null) {
         // set options object
        $this->options = $options;
        if (!$this->options) {
            $this->options = [];
        }

        $this->tokenizer = new StringTokenizer();
        if (isset($this->options['tokenizer'])) {
           $this->tokenizer = $this->options['tokenizer'];
        }

        $this->reset();
    }

    /**
     * reset the bayes class
     * @return Bayes
     */
    public function reset() {
        // hashmap of our category names
        $this->categories = [];

        // document frequency table for each of our categories
        // => for each category, how often were documents mapped to it
        $this->docCount = [];

        // number of documents we have learned from
        $this->totalDocuments = 0;

        // initialize our vocabulary and its size
        $this->vocabulary = [];
        $this->vocabularySize = 0;

        // for each category, how many words total were mapped to it
        $this->wordCount = [];

        // word frequency table for each category
        // => for each category, how frequent was a given word mapped to it
        $this->wordFrequencyCount = [];

        // array of stopwords
        $this->stopWords = [];

        return $this;
    }

    /**
     * deserialize from json
     * @param  object $json string or array
     * @return Bayes
     */
    public function fromJson($json) {
        $result = $json;
        // deserialize from json
        if (is_string($json)) {
            $result = json_decode($json);
        }

        $this->reset();

        // deserialize from json
        foreach($this->ser as $k) {
            if (isset($result[$k])) {
                $this->{$k} = $result[$k];
            }
        }

        return $this;
    }

    /**
     * serialize to json
     * @return string the json string
     */
    public function toJson() {
        $result = [];

        // serialize to json
        foreach($this->ser as $k) {
            $result[$k] = $this->{$k};
        }

        return json_encode($reuslt);
    }

    public function initializeCategory($categoryName) {
        if (!isset($this->categories[$categoryName])) {
            $this->docCount[$categoryName] = 0;
            $this->wordCount[$categoryName] = 0;
            $this->wordFrequencyCount[$categoryName] = [];
            $this->categories[$categoryName] = true;
        }

        return $this;
    }

    public function learn($text, $category) {
        $self = $this;

        // initialize category data structures if we've never seen this category
        $self->initializeCategory($category);

        // update our count of how many documents mapped to this category
        $self->docCount[$category]++;

        // update the total number of documents we have learned from
        $self->totalDocuments++;

        // normalize the text into a word array
        $tokens = $self->tokenizer->tokenize($text);

        // get a frequency count for each token in the text
        $frequencyTable = $self->frequencyTable($tokens);

        // Update vocabulary and word frequency count for this category
        foreach($frequencyTable as $token => $frequencyInText) {
            // add this word to our vocabulary if not already existing
            if (!isset($self->vocabulary[$token])) {
                $self->vocabulary[$token] = true;
                $self->vocabularySize++;
            }

            // update the frequency information for this word in this category
            if (!isset($self->wordFrequencyCount[$category][$token])) {
              $self->wordFrequencyCount[$category][$token] = $frequencyInText;
            }
            else {
              $self->wordFrequencyCount[$category][$token] += $frequencyInText;
            }

            // update the count of all words we have seen mapped to this category
            $self->wordCount[$category] += $frequencyInText;
        }

        return $self;
    }

    public function categorize($text) {
        $self = $this;
        $maxProbability = -INF;
        $chosenCategory = null;

        if ($self->totalDocuments > 0) {
            $tokens = $self->tokenizer->tokenize($text);
            $frequencyTable = $self->frequencyTable($tokens);

            // iterate thru our categories to find the one with max probability for this text
            foreach($self->categories as $category => $value) {
                $categoryProbability = $self->docCount[$category] / $self->totalDocuments;
                $logProbability = log($categoryProbability);
                foreach($frequencyTable as $token => $frequencyInText) {
                    $tokenProbability = $self->tokenProbability($token, $category);
                    // console.log('token: %s category: `%s` tokenProbability: %d', token, category, tokenProbability)

                    // determine the log of the P( w | c ) for this word
                    $logProbability += $frequencyInText * log($tokenProbability);
                }

                if ($logProbability > $maxProbability) {
                  $maxProbability = $logProbability;
                  $chosenCategory = $category;
                }
            }
        }

        return $chosenCategory;
    }

    public function tokenProbability($token, $category) {
        // how many times this word has occurred in documents mapped to this category
        $wordFrequencyCount = 0;
        if (isset($this->wordFrequencyCount[$category][$token])) {
            $wordFrequencyCount = $this->wordFrequencyCount[$category][$token];
        }

        // what is the count of all words that have ever been mapped to this category
        $wordCount = $this->wordCount[$category];

        // use laplace Add-1 Smoothing equation
        return ( $wordFrequencyCount + 1 ) / ( $wordCount + $this->vocabularySize );
    }

    public function frequencyTable($tokens) {
        $frequencyTable = [];
        // print(json_encode($tokens));
        foreach($tokens as $token) {
            if (!isset($frequencyTable[$token])) {
                $frequencyTable[$token] = 1;
            }
            else {
                $frequencyTable[$token]++;
            }
        }

        return $frequencyTable;
    }
}
