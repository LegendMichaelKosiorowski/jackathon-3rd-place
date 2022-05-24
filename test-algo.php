<?php


function mostCommonCharacters(string $input): array
{
    $listOfCharacters = [];
    foreach(mb_str_split($input) as $character) {
        if (!isset($listOfCharacters[$character])) {
            $listOfCharacters[$character] = 0;
        }
        $listOfCharacters[$character]++;
    }
    asort($listOfCharacters);
    $listOfCharacters = array_reverse($listOfCharacters);
    $listOfCharacters = array_values(array_flip($listOfCharacters));
    return $listOfCharacters;
}


function compress(string $input): string
{
    $characters = mostCommonCharacters($input);
    $node = new BinaryNode($characters[0]);
    unset($characters[0]);
    $left = true;
    $lastNode = null;
    $steps = 0b0;
    print_r($characters);die;
    foreach($characters as $character) {
        $steps++;

        print $steps . "\n";
        $newNode = new BinaryNode($character);

//        if ($left) {
//            $node->addLeft($newNode);
//            $nextNode = $newNode;
//            $left = false;
//            continue;
//        }
//        if (!$left) {
//            $node->addRight($newNode);
//            $node = $nextNode;
//            $nextNode = $newNode;
//            $left = true;
//        }

    }
    $output = $input;

    return $output;
}

function decompress(string $input): string
{
    $output = $input;
    return $output;
}

function test(): void
{
    $files = scandir('fixtures');
    $ratios = [];

    foreach ($files as $file) {
//        if (!preg_match('/\.(txt)$/', $file)) {
        if (!preg_match('/\.(css|json|txt)$/', $file)) {
            continue;
        }

        $input = file_get_contents('fixtures/' . $file);

        $compressed = compress($input);
        $decompressed = decompress($compressed);

        if ($decompressed !== $input) {
            echo "FAIL: Outputs do not match!\n";
        }

        $ratio = (1 - (mb_strlen($compressed) / mb_strlen($input))) * 100;
        $ratios[$file] = $ratio;
        echo 'File: ' . $file . ', Ratio: ' . round($ratio) . "%\n";
    }

    $ratioAverage = array_reduce($ratios, fn ($carry, $ratio) => $carry + $ratio) / count($ratios);

    echo 'Average Compression Ratio: ' . round($ratioAverage, 2) . "%\n";
}

test();


class BinaryNode {

    protected $value;
    protected $left;
    protected $right;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function addLeft(BinaryNode $value) {
        $this->left = $value;
    }

    public function addRight(BinaryNode $value) {
        $this->right = $value;
    }

}