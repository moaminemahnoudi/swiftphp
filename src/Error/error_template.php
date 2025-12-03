<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($errorType) ?> - SwiftPHP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-bottom: 1px solid #334155;
            padding: 1.5rem 2rem;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #f97316;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .debug-badge {
            background: #dc2626;
            color: white;
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Main Content */
        .main-content {
            padding: 2rem;
        }

        /* Error Card */
        .error-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .error-header {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            padding: 2rem;
            color: white;
        }

        .error-type {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .error-message {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.4;
            margin-bottom: 1.5rem;
        }

        .error-location {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            font-size: 0.875rem;
            opacity: 0.9;
        }

        .location-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .location-label {
            font-weight: 600;
            min-width: 60px;
        }

        .location-value {
            font-family: 'Monaco', 'Menlo', monospace;
            background: rgba(0, 0, 0, 0.2);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        /* Tabs */
        .tabs {
            display: flex;
            background: #0f172a;
            border-bottom: 1px solid #334155;
        }

        .tab {
            padding: 1rem 1.5rem;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
            font-weight: 500;
            color: #94a3b8;
        }

        .tab:hover {
            color: #e2e8f0;
            background: #1e293b;
        }

        .tab.active {
            color: #f97316;
            border-bottom-color: #f97316;
            background: #1e293b;
        }

        .tab-content {
            display: none;
            padding: 2rem;
        }

        .tab-content.active {
            display: block;
        }

        /* Code Snippet */
        .code-container {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            overflow: hidden;
        }

        .code-header {
            background: #1e293b;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #334155;
            font-size: 0.875rem;
            font-family: 'Monaco', 'Menlo', monospace;
            color: #94a3b8;
        }

        .code-lines {
            padding: 1rem 0;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.875rem;
            line-height: 1.6;
            overflow-x: auto;
        }

        .code-line {
            display: flex;
            padding: 0.25rem 1rem;
            transition: background 0.1s;
        }

        .code-line:hover {
            background: #1e293b;
        }

        .code-line.error-line {
            background: rgba(220, 38, 38, 0.1);
            border-left: 3px solid #dc2626;
        }

        .line-number {
            color: #475569;
            min-width: 50px;
            text-align: right;
            user-select: none;
            padding-right: 1.5rem;
        }

        .error-line .line-number {
            color: #dc2626;
            font-weight: 600;
        }

        .line-content {
            color: #cbd5e1;
            white-space: pre;
        }

        /* AI Hints */
        .hint-card {
            background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            color: white;
        }

        .hint-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .hint-icon {
            font-size: 2rem;
        }

        .hint-title {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .hint-category {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-left: 0.5rem;
        }

        .hint-section {
            margin-top: 1.5rem;
        }

        .hint-section-title {
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .hint-list {
            list-style: none;
            padding-left: 0;
        }

        .hint-list li {
            padding: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
        }

        .hint-list li::before {
            content: "→";
            position: absolute;
            left: 0;
            color: rgba(255, 255, 255, 0.6);
        }

        .quick-fixes {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            padding: 1rem;
        }

        .quick-fixes li::before {
            content: "✓";
        }

        /* Stack Trace */
        .stack-trace {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.8125rem;
            line-height: 1.6;
            overflow-x: auto;
            color: #94a3b8;
        }

        .stack-frame {
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background: #1e293b;
            border-radius: 6px;
            border-left: 3px solid #475569;
        }

        .stack-frame:hover {
            border-left-color: #f97316;
        }

        .frame-location {
            color: #f97316;
            font-weight: 600;
        }

        .frame-file {
            color: #94a3b8;
            margin-top: 0.25rem;
        }

        /* Solutions */
        .solution-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .solution-title {
            font-weight: 600;
            margin-bottom: 1rem;
            color: #f97316;
        }

        .solution-code {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 6px;
            padding: 1rem;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.875rem;
            overflow-x: auto;
            color: #cbd5e1;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 2rem;
            color: #64748b;
            font-size: 0.875rem;
        }

        .footer a {
            color: #f97316;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }

            .error-header {
                padding: 1.5rem;
            }

            .error-message {
                font-size: 1.25rem;
            }

            .tabs {
                overflow-x: auto;
            }

            .tab {
                padding: 0.75rem 1rem;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <div class="logo-icon">⚡</div>
                SwiftPHP
            </div>
            <div class="debug-badge">Debug Mode</div>
        </div>
    </div>

    <div class="container main-content">
        <!-- Error Card -->
        <div class="error-card">
            <div class="error-header">
                <div class="error-type"><?= htmlspecialchars($errorType) ?></div>
                <div class="error-message"><?= htmlspecialchars($message) ?></div>
                <div class="error-location">
                    <div class="location-item">
                        <span class="location-label">File:</span>
                        <span class="location-value"><?= htmlspecialchars($file) ?></span>
                    </div>
                    <div class="location-item">
                        <span class="location-label">Line:</span>
                        <span class="location-value"><?= htmlspecialchars($line) ?></span>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <div class="tab active" onclick="switchTab(event, 'code')">Code</div>
                <div class="tab" onclick="switchTab(event, 'hints')">AI Hints</div>
                <div class="tab" onclick="switchTab(event, 'solutions')">Solutions</div>
                <div class="tab" onclick="switchTab(event, 'trace')">Stack Trace</div>
            </div>

            <!-- Code Tab -->
            <div id="code" class="tab-content active">
                <?php if (!empty($codeSnippet)): ?>
                <div class="code-container">
                    <div class="code-header"><?= htmlspecialchars($file) ?></div>
                    <div class="code-lines">
                        <?php foreach ($codeSnippet as $lineData): ?>
                            <div class="code-line <?= $lineData['highlight'] ? 'error-line' : '' ?>">
                                <span class="line-number"><?= $lineData['line'] ?></span>
                                <span class="line-content"><?= htmlspecialchars($lineData['code']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                    <p style="color: #64748b;">No code snippet available.</p>
                <?php endif; ?>
            </div>

            <!-- AI Hints Tab -->
            <div id="hints" class="tab-content">
                <div class="hint-card">
                    <div class="hint-header">
                        <div class="hint-icon"><?= $aiHint['icon'] ?></div>
                        <div>
                            <div class="hint-title">
                                <?= htmlspecialchars($aiHint['title']) ?>
                                <span class="hint-category"><?= htmlspecialchars($aiHint['category']) ?></span>
                            </div>
                            <div style="opacity: 0.8; font-size: 0.875rem;">AI-powered analysis</div>
                        </div>
                    </div>

                    <div class="hint-section">
                        <div class="hint-section-title">🔍 Problem</div>
                        <p><?= htmlspecialchars($aiHint['problem']) ?></p>
                    </div>

                    <div class="hint-section">
                        <div class="hint-section-title">🤔 Likely Causes</div>
                        <ul class="hint-list">
                            <?php foreach ($aiHint['likely_causes'] as $cause): ?>
                                <li><?= htmlspecialchars($cause) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="hint-section">
                        <div class="hint-section-title">⚡ Quick Fixes</div>
                        <div class="quick-fixes">
                            <ul class="hint-list">
                                <?php foreach ($aiHint['quick_fixes'] as $fix): ?>
                                    <li><?= htmlspecialchars($fix) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Solutions Tab -->
            <div id="solutions" class="tab-content">
                <?php if (!empty($solution)): ?>
                    <?php foreach ($solution as $sol): ?>
                        <div class="solution-card">
                            <div class="solution-title">💡 <?= htmlspecialchars($sol['title']) ?></div>
                            <pre class="solution-code"><?= htmlspecialchars($sol['code']) ?></pre>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #64748b;">No specific solutions available for this error.</p>
                <?php endif; ?>
            </div>

            <!-- Stack Trace Tab -->
            <div id="trace" class="tab-content">
                <div class="stack-trace">
                    <?php
                    $traceLines = explode("\n", $trace);
                    foreach ($traceLines as $traceLine) {
                        if (preg_match('/#(\d+)\s+(.+)/', $traceLine, $matches)) {
                            echo '<div class="stack-frame">';
                            echo '<div class="frame-location">#' . htmlspecialchars($matches[1]) . '</div>';
                            echo '<div class="frame-file">' . htmlspecialchars($matches[2]) . '</div>';
                            echo '</div>';
                        } else {
                            echo '<div>' . htmlspecialchars($traceLine) . '</div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>SwiftPHP Framework • <a href="https://github.com/moaminemahnoudi/swiftphp" target="_blank">Documentation</a> • <a href="https://github.com/moaminemahnoudi/swiftphp/issues" target="_blank">Report Issue</a></p>
    </div>

    <script>
        function switchTab(event, tabName) {
            // Hide all tab contents
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.remove('active'));

            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Show selected tab content
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked tab
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>
