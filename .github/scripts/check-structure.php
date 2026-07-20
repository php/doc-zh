<?php
/**
 * check-structure.php - Compares the block skeleton of a translated file with
 * the matching doc-en file to detect structural drift (an extra <note>, a list
 * turned into a paragraph, etc.).
 *
 * What is NOT compared: inline prose (the text of a <para>, a <title>...), which
 * legitimately differs from one language to another. We stop at the boundary of
 * text containers.
 *
 * Key point: each file is compared against doc-en *at the revision the file says
 * it mirrors* (the "EN-Revision: <hash>" comment), fetched via `git show`. As a
 * result, a file that lags behind sync does not produce a false positive, since
 * it is compared with the English it actually translated.
 *
 * Expected layout: the translation repository is at the root (current
 * directory), doc-en is in the `en/` subdirectory.
 *
 * Usage (from CI):
 *   git diff --name-only ... | php .github/scripts/check-structure.php
 *     STDIN  one .xml path per line (relative to the repo root)
 * Output is GitHub Actions annotations (::error); the script exits non-zero if
 * at least one divergence is found.
 */

// --- DocBook vocabulary ---------------------------------------------------

// Attributes that are part of the "structure": they are included in an
// element's signature (a role= or an xml:id that changes = drift).
const STRUCTURAL_ATTRIBUTES = ['role', 'choice', 'class', 'xml:id', 'rep'];

// Elements whose content is text (inline). We record the element itself but do
// NOT descend into it: its prose is the translator's business.
const TEXT_CONTAINERS = [
    'para', 'simpara', 'term', 'title', 'titleabbrev', 'refpurpose', 'refname',
    'member', 'entry', 'literallayout', 'programlisting', 'screen', 'seg',
    'segtitle', 'synopsis',
];

// --- Skeleton construction ------------------------------------------------

/**
 * Replaces every named entity (&reftitle.intro;, &warn.foo;, ...) with a plain
 * "E" text. Two reasons:
 *   1. without this, the DOM refuses to parse (undeclared entities outside a DTD);
 *   2. it is symmetric - EN and the translation have the same entities at the
 *      same places, so replacing them the same way on both sides creates no
 *      artificial difference.
 * Predefined XML entities (&amp; &lt; ...) and numeric ones are left intact.
 */
function entitiesToPlaceholder(string $xml): string
{
    $namedEntity = '/&(?!(?:amp|lt|gt|quot|apos|#\d+|#x[0-9a-fA-F]+);)[\w.:-]+;/';
    return preg_replace($namedEntity, 'E', $xml);
}

/**
 * An element's signature: its name followed by its structural attributes.
 * E.g. <refsect1 role="description"> gives "refsect1(role=description)".
 */
function elementSignature(DOMElement $element): string
{
    $attributes = [];
    foreach (STRUCTURAL_ATTRIBUTES as $name) {
        $value = $element->getAttribute($name);
        if ($value !== '') {
            $attributes[] = "$name=$value";
        }
    }

    if ($attributes === []) {
        return $element->nodeName;
    }
    return $element->nodeName . '(' . implode(',', $attributes) . ')';
}

/**
 * Walks the tree depth-first and appends each element's signature (prefixed by
 * its depth, in spaces) into $skeleton. We stop at the boundary of text
 * containers: the container is recorded but not its content.
 */
function collectSkeleton(DOMElement $element, string $depth, array &$skeleton): void
{
    // Translator credits are specific to the translation (absent from doc-en):
    // we ignore them so as not to report a false drift.
    $isTranslatorCredits = $element->nodeName === 'authorgroup'
        && str_starts_with($element->getAttribute('xml:id'), 'translators');
    if ($isTranslatorCredits) {
        return;
    }

    $skeleton[] = $depth . elementSignature($element);

    // Text container: we do not enter it (its prose may diverge).
    if (in_array($element->nodeName, TEXT_CONTAINERS, true)) {
        return;
    }

    foreach ($element->childNodes as $child) {
        if ($child->nodeType === XML_ELEMENT_NODE) {
            collectSkeleton($child, $depth . ' ', $skeleton);
        }
    }
}

/**
 * Skeleton of an XML document given as a string: the flat list of its block
 * element signatures. Returns ['<<INVALID>>'] if the XML is unreadable.
 */
function buildSkeleton(string $xml): array
{
    $document = new DOMDocument();
    libxml_use_internal_errors(true); // we handle parse errors ourselves
    $parsed = $document->loadXML(entitiesToPlaceholder($xml), LIBXML_NONET);
    libxml_clear_errors();

    if (!$parsed || !$document->documentElement) {
        return ['<<INVALID>>'];
    }

    $skeleton = [];
    collectSkeleton($document->documentElement, '', $skeleton);
    return $skeleton;
}

// --- doc-en access and comparison -----------------------------------------

/**
 * Content of a doc-en file at a given revision (`git show <hash>:<path>`), or
 * null if the file did not exist at that revision.
 */
function docEnFileAtRevision(string $enRepo, string $hash, string $relativePath): ?string
{
    $command = sprintf(
        'git -C %s show %s:%s 2>/dev/null',
        escapeshellarg($enRepo),
        escapeshellarg($hash),
        escapeshellarg($relativePath)
    );
    $content = shell_exec($command);
    return ($content === null || $content === '') ? null : $content;
}

/**
 * First position where two skeletons differ, as [enLine, translationLine], or
 * null if they are identical. A missing line (shorter skeleton) is represented
 * by "(none)".
 */
function firstDivergence(array $enSkeleton, array $translationSkeleton): ?array
{
    $length = max(count($enSkeleton), count($translationSkeleton));
    for ($i = 0; $i < $length; $i++) {
        $enLine = $enSkeleton[$i] ?? '';
        $translationLine = $translationSkeleton[$i] ?? '';
        if ($enLine !== $translationLine) {
            return [
                trim($enSkeleton[$i] ?? '(none)'),
                trim($translationSkeleton[$i] ?? '(none)'),
            ];
        }
    }
    return null;
}

// --- Input / output -------------------------------------------------------

/** Reads the (.xml) paths given on standard input, one per line. */
function readPathsFromStdin(): array
{
    $paths = [];
    foreach (explode("\n", stream_get_contents(STDIN)) as $line) {
        $path = trim($line);
        if ($path !== '' && str_ends_with($path, '.xml')) {
            $paths[] = $path;
        }
    }
    return $paths;
}

/**
 * Emits each divergence as a GitHub Actions annotation (::error). The path is
 * relative to the translation repo root, so the annotation lands on the right
 * file in the PR. The message is in English (contributor-facing).
 */
function printAnnotations(array $violations): void
{
    foreach ($violations as [$file, $enLine, $translationLine, $enCount, $translationCount]) {
        $message = sprintf(
            'structure differs from doc-en (EN: %s | translation: %s) [blocks EN=%d translation=%d]',
            $enLine, $translationLine, $enCount, $translationCount
        );
        printf("::error file=%s::%s\n", $file, $message);
    }
}

// --- Main program ---------------------------------------------------------
//
// Expected layout: the translation repository is at the root (current
// directory), doc-en is in the `en/` subdirectory.

$workspace = getcwd();
$enRepo = "$workspace/en";

$violations = [];   // each entry: [path, enLine, translationLine, enBlocks, translationBlocks]
$checkedCount = 0;

foreach (readPathsFromStdin() as $relativePath) {
    $translationPath = "$workspace/$relativePath";
    if (!is_file($translationPath)) {
        continue;
    }
    $translationXml = file_get_contents($translationPath);

    // Which EN revision does this translation claim to mirror?
    if (!preg_match('/EN-Revision:\s*([0-9a-f]{40})/', $translationXml, $match)) {
        continue; // no hash: we do not know what to compare against
    }
    $enRevision = $match[1];

    $enXml = docEnFileAtRevision($enRepo, $enRevision, $relativePath);
    if ($enXml === null) {
        continue; // file absent on the EN side at that hash (new, renamed...)
    }

    $checkedCount++;
    $enSkeleton = buildSkeleton($enXml);
    $translationSkeleton = buildSkeleton($translationXml);

    $divergence = firstDivergence($enSkeleton, $translationSkeleton);
    if ($divergence === null) {
        continue; // identical structures: nothing to report
    }

    [$enLine, $translationLine] = $divergence;
    $violations[] = [
        $relativePath, $enLine, $translationLine,
        count($enSkeleton), count($translationSkeleton),
    ];
}

printAnnotations($violations);
fprintf(STDERR, "checked=%d divergent=%d\n", $checkedCount, count($violations));

exit($violations ? 1 : 0);
