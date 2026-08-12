import os
import re

directory = r'd:\Ryaze.my.id\resources\views'
pattern_bg = re.compile(r'\b(bg-(white|slate-(50|100|200)))\b')
pattern_text = re.compile(r'\b(text-(slate|gray|black)-(700|800|900|black))\b')

issues = []

for root, _, files in os.walk(directory):
    for file in files:
        if file.endswith('.blade.php'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8') as f:
                try:
                    lines = f.readlines()
                    for i, line in enumerate(lines):
                        has_bg = pattern_bg.search(line)
                        has_text = pattern_text.search(line)
                        
                        is_issue = False
                        
                        if has_bg and 'dark:bg-' not in line and 'after:bg-white' not in line:
                            issues.append(f"{filepath}:{i+1}: Missing dark:bg- -> {line.strip()}")
                            is_issue = True
                        if has_text and 'dark:text-' not in line and not is_issue:
                            issues.append(f"{filepath}:{i+1}: Missing dark:text- -> {line.strip()}")
                except Exception as e:
                    pass

print(f"Found {len(issues)} issues")
with open(r'd:\Ryaze.my.id\dark_mode_issues.txt', 'w', encoding='utf-8') as f:
    for issue in issues:
        f.write(issue + '\n')
