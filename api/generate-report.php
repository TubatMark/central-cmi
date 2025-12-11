<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/config.php';
require_once __DIR__ . '/../database/auth.php';
require_once __DIR__ . '/../config/ai-config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_role(['secretariat']);

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

try {
    $pdo = $GLOBALS['pdo'];
    
    // Get filter parameters
    $period = $input['period'] ?? 'monthly';
    $agency = $input['agency'] ?? 'all';
    $cluster = $input['cluster'] ?? 'all';
    $reportTitle = $input['title'] ?? 'Activity Report';
    $author = $input['author'] ?? 'WESMAARRDEC';
    
    // Fetch activities from database
    $activities = fetchActivities($pdo, $period, $agency, $cluster);
    
    if (empty($activities)) {
        echo json_encode([
            'success' => false,
            'error' => 'No activities found for the selected filters'
        ]);
        exit;
    }
    
    // Generate narrative using Groq AI
    $narrative = generateNarrative($activities, $reportTitle, $period);
    
    if (!$narrative) {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to generate narrative report'
        ]);
        exit;
    }
    
    // Generate DOCX file
    $filename = generateDocx($narrative, $activities, $reportTitle, $author);
    
    echo json_encode([
        'success' => true,
        'message' => 'Report generated successfully',
        'filename' => $filename,
        'download_url' => BASE_URL . 'api/download-report.php?file=' . urlencode($filename),
        'narrative' => $narrative
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}

/**
 * Fetch activities from database based on filters
 */
function fetchActivities($pdo, $period, $agency, $cluster) {
    $sql = "SELECT a.*, u.firstName, u.lastName, u.agency, u.position
            FROM Activity a
            LEFT JOIN User u ON a.created_by = u.UserID
            WHERE 1=1";
    $params = [];
    
    // Period filter
    $now = new DateTime();
    switch ($period) {
        case 'monthly':
            $startDate = $now->modify('first day of this month')->format('Y-m-d');
            $endDate = $now->modify('last day of this month')->format('Y-m-d');
            break;
        case 'quarterly':
            $quarter = ceil($now->format('n') / 3);
            $startMonth = ($quarter - 1) * 3 + 1;
            $startDate = $now->setDate($now->format('Y'), $startMonth, 1)->format('Y-m-d');
            $endDate = $now->modify('+2 months')->modify('last day of this month')->format('Y-m-d');
            break;
        case 'annually':
            $startDate = $now->format('Y') . '-01-01';
            $endDate = $now->format('Y') . '-12-31';
            break;
        default:
            $startDate = $now->modify('-30 days')->format('Y-m-d');
            $endDate = (new DateTime())->format('Y-m-d');
    }
    
    $sql .= " AND (a.reported_period_start >= ? OR a.reported_period_end <= ?)";
    $params[] = $startDate;
    $params[] = $endDate;
    
    // Agency filter
    if ($agency !== 'all') {
        $sql .= " AND u.agency = ?";
        $params[] = $agency;
    }
    
    // Cluster filter
    if ($cluster !== 'all') {
        $sql .= " AND u.position = ?";
        $params[] = $cluster;
    }
    
    $sql .= " ORDER BY a.reported_period_start DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Generate narrative report using Groq AI
 */
function generateNarrative($activities, $title, $period) {
    // Prepare activity data for AI
    $activitySummary = [];
    $totalActivities = count($activities);
    $completedCount = 0;
    $inProgressCount = 0;
    $pendingCount = 0;
    
    foreach ($activities as $activity) {
        $status = strtolower($activity['status'] ?? 'pending');
        if ($status === 'completed') $completedCount++;
        elseif ($status === 'in_progress' || $status === 'ongoing') $inProgressCount++;
        else $pendingCount++;
        
        $activitySummary[] = [
            'title' => $activity['title'],
            'type' => $activity['type'] ?? 'General',
            'description' => $activity['description'] ?? '',
            'status' => $activity['status'] ?? 'Pending',
            'start_date' => $activity['reported_period_start'],
            'end_date' => $activity['reported_period_end'],
            'representative' => trim(($activity['firstName'] ?? '') . ' ' . ($activity['lastName'] ?? '')),
            'agency' => $activity['agency'] ?? '',
            'location' => $activity['location'] ?? '',
            'participants' => $activity['participants_count'] ?? 0
        ];
    }
    
    $prompt = "You are a professional report writer for WESMAARRDEC (Western Visayas Agriculture, Aquatic and Resources Research and Development Consortium).

Generate a formal narrative report based on the following activity data. The report should be professional, detailed, and suitable for official documentation.

REPORT TITLE: {$title}
REPORTING PERIOD: {$period}

STATISTICS:
- Total Activities: {$totalActivities}
- Completed: {$completedCount}
- In Progress: {$inProgressCount}
- Pending: {$pendingCount}

ACTIVITY DATA:
" . json_encode($activitySummary, JSON_PRETTY_PRINT) . "

Please generate a comprehensive narrative report with the following sections:

1. EXECUTIVE SUMMARY
   - Brief overview of the reporting period
   - Key highlights and achievements
   - Overall completion statistics

2. DETAILED ACTIVITY NARRATIVES
   - For each activity, provide a narrative description
   - Include context, objectives, and outcomes
   - Mention the responsible representative and agency

3. CLUSTER/DEPARTMENT ANALYSIS
   - Group activities by type or cluster
   - Analyze performance patterns

4. CONCLUSIONS AND RECOMMENDATIONS
   - Summary of accomplishments
   - Areas for improvement
   - Recommendations for next period

Write in formal English. Be specific and detailed. Use proper paragraph structure.";

    $response = callGroqAPI($prompt);
    
    return $response;
}

/**
 * Call Groq API
 */
function callGroqAPI($prompt) {
    $data = [
        'model' => GROQ_MODEL,
        'messages' => [
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'temperature' => 0.7,
        'max_tokens' => 4000
    ];
    
    $ch = curl_init(GROQ_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . GROQ_API_KEY
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        error_log("Groq API cURL error: " . $error);
        return null;
    }
    
    if ($httpCode !== 200) {
        error_log("Groq API HTTP error: " . $httpCode . " - " . $response);
        return null;
    }
    
    $result = json_decode($response, true);
    
    if (isset($result['choices'][0]['message']['content'])) {
        return $result['choices'][0]['message']['content'];
    }
    
    error_log("Groq API unexpected response: " . $response);
    return null;
}

/**
 * Generate DOCX file
 */
function generateDocx($narrative, $activities, $title, $author) {
    $uploadsDir = __DIR__ . '/../uploads/reports';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0777, true);
    }
    
    $filename = 'report_' . date('Y-m-d_His') . '.docx';
    $filepath = $uploadsDir . '/' . $filename;
    
    // Create DOCX structure
    $zip = new ZipArchive();
    if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new Exception('Cannot create DOCX file');
    }
    
    // [Content_Types].xml
    $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
    <Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>
</Types>';
    $zip->addFromString('[Content_Types].xml', $contentTypes);
    
    // _rels/.rels
    $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>';
    $zip->addFromString('_rels/.rels', $rels);
    
    // word/_rels/document.xml.rels
    $docRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
    $zip->addFromString('word/_rels/document.xml.rels', $docRels);
    
    // word/styles.xml
    $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
    <w:style w:type="paragraph" w:styleId="Title">
        <w:name w:val="Title"/>
        <w:pPr><w:jc w:val="center"/></w:pPr>
        <w:rPr><w:b/><w:sz w:val="48"/></w:rPr>
    </w:style>
    <w:style w:type="paragraph" w:styleId="Heading1">
        <w:name w:val="Heading 1"/>
        <w:rPr><w:b/><w:sz w:val="32"/><w:color w:val="1a56db"/></w:rPr>
    </w:style>
    <w:style w:type="paragraph" w:styleId="Heading2">
        <w:name w:val="Heading 2"/>
        <w:rPr><w:b/><w:sz w:val="28"/></w:rPr>
    </w:style>
    <w:style w:type="paragraph" w:styleId="Normal">
        <w:name w:val="Normal"/>
        <w:rPr><w:sz w:val="24"/></w:rPr>
    </w:style>
</w:styles>';
    $zip->addFromString('word/styles.xml', $styles);
    
    // word/document.xml - Main content
    $documentXml = createDocumentXml($narrative, $title, $author, count($activities));
    $zip->addFromString('word/document.xml', $documentXml);
    
    $zip->close();
    
    return $filename;
}

/**
 * Create document.xml content
 */
function createDocumentXml($narrative, $title, $author, $activityCount) {
    $date = date('F j, Y');
    
    // Escape special characters for XML
    $title = htmlspecialchars($title, ENT_XML1, 'UTF-8');
    $author = htmlspecialchars($author, ENT_XML1, 'UTF-8');
    
    // Convert narrative to paragraphs
    $narrativeParagraphs = '';
    $lines = explode("\n", $narrative);
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        $escapedLine = htmlspecialchars($line, ENT_XML1, 'UTF-8');
        
        // Check if it's a heading
        if (preg_match('/^#+\s*(.+)$/', $line, $matches)) {
            $headingText = htmlspecialchars(trim($matches[1]), ENT_XML1, 'UTF-8');
            $narrativeParagraphs .= '<w:p><w:pPr><w:pStyle w:val="Heading1"/></w:pPr><w:r><w:t>' . $headingText . '</w:t></w:r></w:p>';
        } elseif (preg_match('/^\d+\.\s*[A-Z]/', $line) || preg_match('/^[A-Z][A-Z\s]+:?\s*$/', $line)) {
            // Section headers
            $narrativeParagraphs .= '<w:p><w:pPr><w:pStyle w:val="Heading1"/></w:pPr><w:r><w:t>' . $escapedLine . '</w:t></w:r></w:p>';
        } elseif (preg_match('/^[-•]\s/', $line)) {
            // Bullet points
            $narrativeParagraphs .= '<w:p><w:r><w:t>• ' . substr($escapedLine, 2) . '</w:t></w:r></w:p>';
        } else {
            // Regular paragraphs
            $narrativeParagraphs .= '<w:p><w:r><w:t>' . $escapedLine . '</w:t></w:r></w:p>';
        }
    }
    
    $document = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
    <w:body>
        <!-- Title -->
        <w:p>
            <w:pPr><w:pStyle w:val="Title"/><w:jc w:val="center"/></w:pPr>
            <w:r><w:rPr><w:b/><w:sz w:val="48"/></w:rPr><w:t>' . $title . '</w:t></w:r>
        </w:p>
        
        <!-- Subtitle -->
        <w:p>
            <w:pPr><w:jc w:val="center"/></w:pPr>
            <w:r><w:rPr><w:sz w:val="24"/><w:color w:val="666666"/></w:rPr><w:t>WESMAARRDEC Activity Management System</w:t></w:r>
        </w:p>
        
        <!-- Author and Date -->
        <w:p>
            <w:pPr><w:jc w:val="center"/></w:pPr>
            <w:r><w:rPr><w:sz w:val="22"/></w:rPr><w:t>Generated by ' . $author . ' | ' . $date . '</w:t></w:r>
        </w:p>
        
        <!-- Divider -->
        <w:p><w:r><w:t></w:t></w:r></w:p>
        <w:p>
            <w:pPr><w:pBdr><w:bottom w:val="single" w:sz="6" w:space="1" w:color="cccccc"/></w:pBdr></w:pPr>
        </w:p>
        <w:p><w:r><w:t></w:t></w:r></w:p>
        
        <!-- Narrative Content -->
        ' . $narrativeParagraphs . '
        
        <!-- Footer -->
        <w:p><w:r><w:t></w:t></w:r></w:p>
        <w:p>
            <w:pPr><w:pBdr><w:top w:val="single" w:sz="6" w:space="1" w:color="cccccc"/></w:pBdr><w:jc w:val="center"/></w:pPr>
            <w:r><w:rPr><w:sz w:val="20"/><w:color w:val="999999"/></w:rPr><w:t>© ' . date('Y') . ' Central CMI - WESMAARRDEC. All Rights Reserved.</w:t></w:r>
        </w:p>
    </w:body>
</w:document>';
    
    return $document;
}
