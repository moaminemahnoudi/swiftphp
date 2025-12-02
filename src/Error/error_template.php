<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - SwiftPHP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }

        .error-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .error-header {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .error-icon {
            font-size: 64px;
            margin-bottom: 20px;
            animation: bounce 1s ease infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .error-title {
            font-size: 32px;
            font-weight: 700;
            color: #e53e3e;
            margin-bottom: 10px;
        }

        .error-type {
            display: inline-block;
            background: #fed7d7;
            color: #c53030;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .error-message {
            font-size: 18px;
            color: #4a5568;
            line-height: 1.6;
            padding: 20px;
            background: #f7fafc;
            border-radius: 8px;
            border-left: 4px solid #e53e3e;
        }

        .error-location {
            margin-top: 15px;
            font-size: 14px;
            color: #718096;
        }

        .error-location strong {
            color: #2d3748;
        }

        .ai-hint-container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border-left: 6px solid #48bb78;
        }

        .ai-hint-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .ai-icon {
            font-size: 48px;
            animation: pulse 2s ease infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .ai-hint-title {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
        }

        .ai-category {
            display: inline-block;
            background: #c6f6d5;
            color: #22543d;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }

        .ai-section {
            margin-bottom: 25px;
        }

        .ai-section-title {
            font-size: 16px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ai-section-icon {
            font-size: 20px;
        }

        .ai-problem {
            background: #fff5f5;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #fc8181;
            color: #742a2a;
            line-height: 1.6;
        }

        .ai-list {
            list-style: none;
            padding: 0;
        }

        .ai-list li {
            padding: 10px 15px;
            margin-bottom: 8px;
            background: #f7fafc;
            border-radius: 6px;
            border-left: 3px solid #4299e1;
            transition: all 0.2s;
        }

        .ai-list li:hover {
            background: #edf2f7;
            transform: translateX(5px);
        }

        .ai-list li::before {
            content: "→";
            margin-right: 10px;
            color: #4299e1;
            font-weight: bold;
        }

        .quick-fixes {
            background: #f0fff4;
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #48bb78;
        }

        .quick-fixes li {
            border-left-color: #48bb78;
        }

        .quick-fixes li::before {
            content: "✓";
            color: #48bb78;
        }

        .code-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .code-section-title {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .code-snippet {
            background: #1a202c;
            border-radius: 8px;
            padding: 20px;
            overflow-x: auto;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 14px;
            line-height: 1.6;
        }

        .code-line {
            display: flex;
            gap: 15px;
            padding: 4px 0;
        }

        .code-line-number {
            color: #718096;
            user-select: none;
            min-width: 40px;
            text-align: right;
        }

        .code-line-content {
            color: #e2e8f0;
            flex: 1;
        }

        .code-line.highlight {
            background: rgba(229, 62, 62, 0.2);
            border-left: 4px solid #e53e3e;
            margin-left: -20px;
            padding-left: 16px;
        }

        .code-line.highlight .code-line-number {
            color: #fc8181;
            font-weight: bold;
        }

        .solution-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .solution-card {
            background: #f7fafc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #4299e1;
        }

        .solution-title {
            font-size: 16px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 12px;
        }

        .solution-code {
            background: #1a202c;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 13px;
            line-height: 1.6;
            overflow-x: auto;
        }

        .stack-trace {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .stack-trace pre {
            background: #1a202c;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 13px;
            line-height: 1.6;
            font-family: monospace;
        }

        .footer {
            text-align: center;
            color: white;
            padding: 20px;
            font-size: 14px;
        }

        .footer a {
            color: white;
            text-decoration: underline;
        }

        .toggle-btn {
            background: #4299e1;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            margin-top: 10px;
        }

        .toggle-btn:hover {
            background: #3182ce;
            transform: translateY(-2px);
        }

        .hidden {
            display: none;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .error-header,
            .ai-hint-container,
            .code-section,
            .solution-section,
            .stack-trace {
                padding: 20px;
            }

            .error-title {
                font-size: 24px;
            }

            .ai-hint-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <!-- Error Header -->
        <div class="error-header">
            <div class="error-icon">💥</div>
            <h1 class="error-title">Oops! Something went wrong</h1>
            <span class="error-type"><?= htmlspecialchars($errorType) ?></span>
            
            <div class="error-message">
                <?= htmlspecialchars($message) ?>
            </div>

            <div class="error-location">
                <strong>File:</strong> <?= htmlspecialchars($file) ?><br>
                <strong>Line:</strong> <?= htmlspecialchars($line) ?>
            </div>
        </div>

        <!-- AI-Powered Hint -->
        <div class="ai-hint-container">
            <div class="ai-hint-header">
                <div class="ai-icon"><?= $aiHint['icon'] ?></div>
                <div>
                    <h2 class="ai-hint-title">
                        <?= htmlspecialchars($aiHint['title']) ?>
                        <span class="ai-category"><?= htmlspecialchars($aiHint['category']) ?></span>
                    </h2>
                    <p style="color: #718096; margin-top: 5px;">AI-powered analysis and suggestions</p>
                </div>
            </div>

            <div class="ai-section">
                <h3 class="ai-section-title">
                    <span class="ai-section-icon">🔍</span>
                    What's the Problem?
                </h3>
                <div class="ai-problem">
                    <?= htmlspecialchars($aiHint['problem']) ?>
                </div>
            </div>

            <div class="ai-section">
                <h3 class="ai-section-title">
                    <span class="ai-section-icon">🤔</span>
                    Likely Causes
                </h3>
                <ul class="ai-list">
                    <?php foreach ($aiHint['likely_causes'] as $cause): ?>
                        <li><?= htmlspecialchars($cause) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="ai-section">
                <h3 class="ai-section-title">
                    <span class="ai-section-icon">⚡</span>
                    Quick Fixes
                </h3>
                <div class="quick-fixes">
                    <ul class="ai-list">
                        <?php foreach ($aiHint['quick_fixes'] as $fix): ?>
                            <li><?= htmlspecialchars($fix) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Code Snippet -->
        <?php if (!empty($codeSnippet)): ?>
        <div class="code-section">
            <h3 class="code-section-title">
                <span>📝</span>
                Code Context
            </h3>
            <div class="code-snippet">
                <?php foreach ($codeSnippet as $line): ?>
                    <div class="code-line <?= $line['highlight'] ? 'highlight' : '' ?>">
                        <span class="code-line-number"><?= $line['line'] ?></span>
                        <span class="code-line-content"><?= htmlspecialchars($line['code']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Solutions -->
        <?php if (!empty($solution)): ?>
        <div class="solution-section">
            <h3 class="code-section-title">
                <span>💡</span>
                Suggested Solutions
            </h3>
            <?php foreach ($solution as $sol): ?>
                <div class="solution-card">
                    <div class="solution-title"><?= htmlspecialchars($sol['title']) ?></div>
                    <pre class="solution-code"><?= htmlspecialchars($sol['code']) ?></pre>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Stack Trace (Collapsible) -->
        <?php if (self::isDebugMode()): ?>
        <div class="stack-trace">
            <h3 class="code-section-title">
                <span>🔬</span>
                Stack Trace
            </h3>
            <button class="toggle-btn" onclick="toggleTrace()">Show Full Trace</button>
            <pre id="traceContent" class="hidden"><?= htmlspecialchars($trace) ?></pre>
        </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="footer">
            <p>SwiftPHP v2.0 - Built with ❤️ for developers</p>
            <p><a href="https://github.com/swiftphp/framework" target="_blank">Documentation</a> • <a href="https://github.com/swiftphp/framework/issues" target="_blank">Report Issue</a></p>
        </div>
    </div>

    <script>
        function toggleTrace() {
            const trace = document.getElementById('traceContent');
            const btn = event.target;
            
            if (trace.classList.contains('hidden')) {
                trace.classList.remove('hidden');
                btn.textContent = 'Hide Full Trace';
            } else {
                trace.classList.add('hidden');
                btn.textContent = 'Show Full Trace';
            }
        }
    </script>
</body>
</html>
