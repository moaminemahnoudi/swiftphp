#!/usr/bin/env bash

# Script to add declare(strict_types=1) to all PHP files in src/

find src -name "*.php" -type f | while read file; do
    if ! grep -q "declare(strict_types=1)" "$file"; then
        # Create temp file with strict types declaration
        {
            head -n 1 "$file"  # <?php line
            echo ""
            echo "declare(strict_types=1);"
            tail -n +2 "$file"  # Rest of the file
        } > "$file.tmp"
        mv "$file.tmp" "$file"
        echo "Added strict_types to: $file"
    fi
done
