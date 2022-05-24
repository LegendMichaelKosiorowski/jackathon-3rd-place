<?php


function mostCommonCharacters(string $input, int $amount): array
{
    $listOfCharacters = [];
    foreach(mb_str_split($input) as $character) {
        if (!isset($listOfCharacters[$character])) {
            $listOfCharacters[$character] = 0;
        }
        $listOfCharacters[$character]++;
    }
    asort($listOfCharacters);
    $mostCommon = [];
    for ($i = 1; $i <= $amount; $i++) {
        $mostCommon[] = array_key_last($listOfCharacters);
        unset($listOfCharacters[array_key_last($listOfCharacters)]);
    }
    return $mostCommon;
}

function tryExplodingOn(string $input, string $explodeCharacter): string
{
    $words = explode($explodeCharacter, $input);

//    print_r($words);
    $dups = [];
//    print_r($words);
    foreach($words as $word) {
        if (!isset($dups[$word])) {
            $dups[$word] = 0;
        }
        $dups[$word]++;
    }
    $replaces = [];
    $i = 0;
    foreach($dups as $word => $amount) {
        if ($amount <= 1) {
            continue;
        }
        while(str_contains($input, $chr = mb_chr($i++)) && $chr !== false) {
//            print $i . ' = ' . $chr . "\n";
        }
        $replaces[$word] = $chr;

//        $replaces[$word] = '@' . $i++ . '@';
    }
    uksort($replaces, function($a, $b) {
        return strlen($b) <=> strlen($a);
    });
    $output = $input;
    foreach($replaces as $word => $replacement) {
        $output = str_replace($word, $replacement, $output);
    }

//    print_r($dups);die;
//    sort($dups);
//    print_r($dups);
    // @TODO Potential bug - not checking if char exists in original
    $lineSeparator = mb_chr($i++);
    $recordSeparator = mb_chr($i++);
    $replacesCSV = $lineSeparator . $recordSeparator;
    foreach($replaces as $word => $replace) {
        $replacesCSV .= $word . $recordSeparator . $replace . $lineSeparator;
    }

//    file_put_contents('replaces.txt', json_encode($replaces, JSON_PRETTY_PRINT));
    $output = $replacesCSV . '||' . $output;
//    file_put_contents('result'.microtime(true).'.txt', $output);
    return $output;
}

function compress(string $input): string
{
    $characters = mostCommonCharacters($input, 10);
    $results = [];
    foreach($characters as $char) {
        $result = tryExplodingOn($input, $char);
        $results[mb_strlen($result)] = $result;
    }
    ksort($results);

    return $results[array_key_first($results)];
}

function decompress(string $input): string
{
    $jsonEndsAt = strpos($input, '||');
//    $json = substr($input, 0, $jsonEndsAt);
    $replacesCSV = mb_substr($input, 0, $jsonEndsAt);
    $lS = mb_substr($replacesCSV, 0, 1);
    $rS = mb_substr($replacesCSV, 1, 1);
    $replaces = [];
    foreach(explode($lS, mb_substr($replacesCSV, 2, mb_strlen($replacesCSV) - 2)) as $line) {
        $r = explode($rS, $line);
        if (!isset($r[1])) {
            continue;
        }
        $replaces[$r[0]] = $r[1];
    }


//    $replaces = json_decode($json, true);
//    print_r($json);
    $output = substr($input, $jsonEndsAt + 2, strlen($input));
//    print $output;die;
    asort($replaces);
    $replaces = array_reverse($replaces, true);
    $replaces = array_flip($replaces);
    foreach($replaces as $symbol => $replace) {
        $output = str_replace($symbol, $replace, $output);
    }
//    $output = substr($output, 1, strlen($output));
//    file_put_contents('decompressed.txt', $output);

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
