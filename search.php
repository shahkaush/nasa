<?php
ini_set('memory_limit', '512M');
header('Content-Type: application/json');

// --- Helper: Simple stem for plural-insensitive search
function stem($word) {
    $word = strtolower($word);
    if (substr($word, -3) === "ies") {
        return substr($word, 0, -3) . "y";
    } elseif (substr($word, -1) === "s") {
        return substr($word, 0, -1);
    }
    return $word;
}

$custom_stopwords = [
    "university","science","space","department","state","plant","response","center","http","acces",
    "institute","environment","station","system","condition","school","correspondence","expression",
    "medicine","life","author","technology","study","term","function","engineering","level","day",
    "role","report","group","abstract","editor","development","mechanism","program","journal","animal",
    "los","college","field","earth","body","doi","sample","impact","activity","root","increase","review",
    "risk","interaction","introduction","number","usa","method","ground","time","regard","frontier",
    "work","type","control","note","map","treatment","specialty","sci","production","tion","insight",
    "material","view","lo","city","cancer","pattern","charle","year","coste","is"
];
$custom_stopwords = array_map('strtolower', $custom_stopwords); // normalize


// --- Load preprocessed data
$data = include "keywords.php";

$results = [];
$unique_keywords_count = [];

// --- Prepare unique keywords with counts (apply stemming)
foreach ($data as $pdf) {
    if (isset($pdf['keywords']) && is_array($pdf['keywords'])) {
        foreach ($pdf['keywords'] as $kw) {
            $kw = strtolower(trim($kw));
            if ($kw === "") continue;

            $stemmed_kw = stem($kw);
if (in_array($stemmed_kw, $custom_stopwords)) continue;

            if (!isset($unique_keywords_count[$stemmed_kw])) {
                $unique_keywords_count[$stemmed_kw] = 0;
            }
            $unique_keywords_count[$stemmed_kw]++;
        }
    }
}

// --- Validate input from GET
$searchInput = isset($_GET['keywords']) ? trim($_GET['keywords']) : "";

if ($searchInput === "") {
    echo json_encode([
        "results" => [],
        "unique_keywords" => $unique_keywords_count
    ]);
    exit;
}

$keywords = explode(" ", $searchInput);
$keywords = array_map('stem', $keywords);

// --- Search PDFs
foreach ($data as $pdf) {
    $allMatch = true;

    foreach ($keywords as $searchWord) {
        $searchWord = strtolower($searchWord);

        // Fields to search
        $fieldsToSearch = [
            $pdf['title'] ?? "",
            $pdf['summary'] ?? "",
            implode(" ", $pdf['keywords'] ?? [])
        ];

        $found = false;
        foreach ($fieldsToSearch as $field) {
            if (stripos(stem($field), $searchWord) !== false) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $allMatch = false;
            break;
        }
    }

    if ($allMatch) {
        // Remove keywords field before adding to results
        unset($pdf['keywords']);
        $results[] = $pdf;
    }
}

foreach ($keywords as $kw) {
    if (isset($unique_keywords_count[$kw])) {
        unset($unique_keywords_count[$kw]);
    }
}
// --- Return results
echo json_encode([
    "results" => $results,
    "unique_keywords" => $unique_keywords_count
]);
