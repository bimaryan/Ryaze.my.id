import os, glob

directories = ['d:/Ryaze.my.id/app/Jobs', 'd:/Ryaze.my.id/app/Http/Controllers/Hosting/User', 'd:/Ryaze.my.id/app/Http/Controllers/Hosting/Admin', 'd:/Ryaze.my.id/app/Console/Commands']
for d in directories:
    for root, _, files in os.walk(d):
        for f in files:
            if f.endswith('.php'):
                path = os.path.join(root, f)
                with open(path, 'r', encoding='utf-8') as file:
                    content = file.read()
                
                new_content = content.replace("str_replace('.ryaze.my.id', '', $this->project->ryaze_domain)", "explode('.', $this->project->ryaze_domain)[0]")
                new_content = new_content.replace("str_replace('.ryaze.my.id', '', $project->ryaze_domain)", "explode('.', $project->ryaze_domain)[0]")
                new_content = new_content.replace("str_replace('.ryaze.my.id', '', $p->ryaze_domain)", "explode('.', $p->ryaze_domain)[0]")
                
                if content != new_content:
                    with open(path, 'w', encoding='utf-8') as file:
                        file.write(new_content)
                    print(f'Updated {path}')
