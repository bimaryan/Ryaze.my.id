import os
import re

directory = r'd:\Ryaze.my.id\resources\views'

replacements_bg = [
    (r'\bbg-white\b', 'dark:bg-slate-800'),
    (r'\bbg-slate-50\b', 'dark:bg-slate-800/50'),
    (r'\bbg-slate-100\b', 'dark:bg-slate-700/50'),
    (r'\bbg-gray-50\b', 'dark:bg-slate-800/50'),
    (r'\bbg-gray-100\b', 'dark:bg-slate-700/50'),
    (r'\bhover:bg-slate-50\b', 'dark:hover:bg-slate-700/40'),
    (r'\bhover:bg-slate-100\b', 'dark:hover:bg-slate-700/50'),
]

replacements_text = [
    (r'\btext-slate-900\b', 'dark:text-slate-100'),
    (r'\btext-slate-800\b', 'dark:text-slate-100'),
    (r'\btext-slate-700\b', 'dark:text-slate-200'),
    (r'\btext-slate-600\b', 'dark:text-slate-300'),
    (r'\btext-gray-900\b', 'dark:text-slate-100'),
    (r'\btext-gray-800\b', 'dark:text-slate-100'),
    (r'\btext-gray-700\b', 'dark:text-slate-200'),
    (r'\btext-gray-600\b', 'dark:text-slate-300'),
    (r'\btext-black\b', 'dark:text-white'),
]

total_files_changed = 0

for root, _, files in os.walk(directory):
    for file in files:
        if file.endswith('.blade.php'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original_content = content
            lines = content.split('\n')
            new_lines = []
            
            for line in lines:
                new_line = line
                # Ignore lines that have after:bg-white (toggle buttons)
                if 'after:bg-white' in new_line:
                    new_lines.append(new_line)
                    continue
                
                # Check backgrounds
                has_bg_dark = 'dark:bg-' in new_line
                has_hover_bg_dark = 'dark:hover:bg-' in new_line
                
                for pat, repl in replacements_bg:
                    if re.search(pat, new_line):
                        is_hover = 'hover:' in repl
                        if is_hover and has_hover_bg_dark: continue
                        if not is_hover and has_bg_dark: continue
                        
                        new_line = re.sub(pat, r'\g<0> ' + repl, new_line)
                        # Update flags to avoid adding multiple dark:bg- on the same line
                        if is_hover: has_hover_bg_dark = True
                        else: has_bg_dark = True
                
                # Check text
                has_text_dark = 'dark:text-' in new_line
                has_hover_text_dark = 'dark:hover:text-' in new_line
                
                for pat, repl in replacements_text:
                    if re.search(pat, new_line):
                        is_hover = 'hover:' in repl
                        if is_hover and has_hover_text_dark: continue
                        if not is_hover and has_text_dark: continue
                        
                        new_line = re.sub(pat, r'\g<0> ' + repl, new_line)
                        if is_hover: has_hover_text_dark = True
                        else: has_text_dark = True
                            
                new_lines.append(new_line)
            
            new_content = '\n'.join(new_lines)
            if new_content != original_content:
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                total_files_changed += 1
                print(f"Updated: {filepath}")
                
print(f"Done. Changed files: {total_files_changed}")
