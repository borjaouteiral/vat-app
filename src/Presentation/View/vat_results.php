<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/vat_results.css">
        <title>VAT Results</title>
    </head>
    <body>
        <div class="container">
            <?php
                $summary = $results['summary'] ?? [];
                unset($results['summary']);
            ?>
            
            <div class="main-content">
                <div class="header-actions">
                    <a href="index.php" class="back-button">🏠 HOME</a>
                </div>
                <div class="results-container">                
                    <h1>Processed VAT Numbers</h1>

                    <div class="result-group result-valid">
                        
                        <div class="group-header" onclick="toggleGroup(this)">
                            Valid VAT Numbers
                            <span class="toggle-icon">▶</span>
                        </div>
                        <div id="valid" class="group-content">
                            <label>
                                <input type="checkbox" id="showAllCheckbox" onclick="toggleShowAll(this)">
                                Show All
                            </label>
                            <ul>
                                <?php foreach ($results as $result): ?>
                                    <?php if ($result->status === 'valid'): ?>
                                        <li class="valid">✔️  <?= htmlspecialchars($result->original) ?></li>
                                    <?php endif; ?>
                                    <?php if ($result->status === 'duplicate'): ?>
                                        <li class="duplicate" title="duplicate">👁️ <?= !empty($result->corrected) ? htmlspecialchars($result->corrected) : htmlspecialchars($result->original) ?></li>
                                    <?php endif; ?>
                                    <?php if ($result->status === 'replace'): ?>
                                        <li classe="replace" title="replace">⚠️ <?= !empty($result->corrected) ? htmlspecialchars($result->corrected) : htmlspecialchars($result->original) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            
                            </ul>
                        </div>
                    </div>

                    <div class="result-group result-fixed">
                        <div class="group-header" onclick="toggleGroup(this)">
                            Fixed VAT Numbers
                            <span class="toggle-icon">▶</span>
                        </div>
                        <div class="group-content">
                        <ul>
                            <?php foreach ($results as $result): ?>
                                <?php if ($result->status === 'fixed'): ?>
                                    <?php
                                    // Compare original and corrected, highlight changed part in corrected
                                    $original = $result->original;
                                    $corrected = $result->corrected;
                                    $diffStart = 0;
                                    $diffEnd = strlen($corrected);

                                    // Find first differing character
                                    while ($diffStart < strlen($original) && $diffStart < strlen($corrected) && $original[$diffStart] === $corrected[$diffStart]) {
                                        $diffStart++;
                                    }
                                    // Find last matching character from the end
                                    $oEnd = strlen($original) - 1;
                                    $cEnd = strlen($corrected) - 1;
                                    while ($oEnd >= $diffStart && $cEnd >= $diffStart && $original[$oEnd] === $corrected[$cEnd]) {
                                        $oEnd--;
                                        $cEnd--;
                                    }
                                    $diffEnd = $cEnd + 1;

                                    $before = htmlspecialchars(substr($corrected, 0, $diffStart));
                                    $changed = htmlspecialchars(substr($corrected, $diffStart, $diffEnd - $diffStart));
                                    $after = htmlspecialchars(substr($corrected, $diffEnd));
                                    ?>
                                    <li>
                                        <?= htmlspecialchars($original) ?> → <?= $before ?><strong><?= $changed ?></strong><?= $after ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                        </div>
                    </div>

                    <div class="result-group result-invalid">
                        <div class="group-header" onclick="toggleGroup(this)">
                            Invalid VAT Numbers
                            <span class="toggle-icon">▶</span>
                        </div>
                        <div class="group-content">
                            <ul>
                                <?php foreach ($results as $result): ?>
                                    <?php if ($result->status === 'invalid'): ?>
                                        <li>❌ <?= htmlspecialchars($result->original) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
            <aside class="sidebar">            

                <div class="summary-container">
                    <h3>Summary</h3>
                    <div class="summary-grid">
                        <div class="summary-card valid">
                            <span class="status-label">✔  Valid</span>
                            <span class="status-count"><?= isset($summary['valid']) ? $summary['valid'] : 0; ?></span>
                        </div>
                        <div class="summary-card fixed">
                            <span class="status-label">🔧 Fixed</span>
                            <span class="status-count"><?= isset($summary['fixed']) ? $summary['fixed'] : 0; ?></span>
                        </div>
                        <div class="summary-card invalid">
                            <span class="status-label">❌ Invalid</span>
                            <span class="status-count"><?= isset($summary['invalid']) ? $summary['invalid'] : 0; ?></span>
                        </div>
                        <div class="summary-card duplicate">
                            <span class="status-label">👁️ Duplicate</span>
                            <span class="status-count"><?= isset($summary['duplicate']) ? $summary['duplicate'] : 0; ?></span>
                        </div>
                        <div class="summary-card replace">
                            <span class="status-label">⚠️ Replace</span>
                            <span class="status-count"><?= isset($summary['replace']) ? $summary['replace'] : 0; ?></span>
                        </div>
                    </div>
                </div>
                <!--<br>
                <h3>Icon Legend</h3>
                <ul class="icon-legend">
                    <li class="icon-valid">
                        <span class="icon">✔️</span> Valid VAT
                    </li>
                    <li class="icon-duplicate">
                        <span class="icon">👁️</span> Duplicate Number
                    </li>
                    <li class="icon-replace">
                        <span class="icon">⚠️</span> Replaced ID
                    </li>
                    <li class="icon-invalid">
                        <span class="icon">❌</span> Invalid Number
                    </li>
                </ul>-->
            </aside>
        </div>
        <script>
            function toggleGroup(header) {
                const content = header.nextElementSibling;
                header.classList.toggle('active');
                content.classList.toggle('open');
            }

            function toggleShowAll(checkbox) {
                const list = document.getElementById('valid');
                const items = list.querySelectorAll('li');

                if (checkbox.checked) {
                    // Show all items
                    items.forEach(li => li.style.display = 'list-item');
                } else {
                    // Only show items with class "valid"
                    items.forEach(li => {
                        li.style.display = li.classList.contains('valid') ? 'list-item' : 'none';
                    });
                }
            }
        </script>
    </body>
</html>

